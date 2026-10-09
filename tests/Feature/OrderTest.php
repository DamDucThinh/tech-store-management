<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private Employee $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = Employee::factory()->withProfile(['name' => 'Trần Thị Bán'])->create();
        $this->actingAs($this->staff);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, mixed>
     */
    private function orderData(array $items, array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Phạm Thu Trang',
            'customer_phone' => '0934567890',
            'items' => $items,
        ], $overrides);
    }

    // ---------- Tạo đơn hàng ----------

    public function test_create_order_page_lists_only_selling_products(): void
    {
        Product::factory()->create(['name' => 'Sản phẩm đang bán']);
        Product::factory()->stopped()->create(['name' => 'Sản phẩm ngừng bán']);

        $this->get(route('orders.create'))
            ->assertOk()
            ->assertSee('Sản phẩm đang bán')
            ->assertDontSee('Sản phẩm ngừng bán');
    }

    public function test_order_is_created_with_items_total_and_pending_status(): void
    {
        $phone = Product::factory()->create(['price' => 5000000]);
        $cable = Product::factory()->create(['price' => 150000]);

        $response = $this->post(route('orders.store'), $this->orderData([
            ['product_id' => $phone->id, 'quantity' => 2],
            ['product_id' => $cable->id, 'quantity' => 3],
        ]));

        $order = Order::sole();
        $response->assertRedirect(route('orders.show', $order));

        $this->assertSame($this->staff->id, $order->employee_id);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertEquals(10450000, $order->total_amount);
        $this->assertCount(2, $order->items);
    }

    public function test_order_item_keeps_price_at_time_of_order(): void
    {
        $product = Product::factory()->create(['price' => 1000000]);

        $this->post(route('orders.store'), $this->orderData([['product_id' => $product->id, 'quantity' => 1]]));
        $product->update(['price' => 1500000]);

        $order = Order::sole();
        $this->assertEquals(1000000, $order->items->first()->price);
        $this->assertEquals(1000000, $order->total_amount);
    }

    public function test_price_sent_from_form_is_ignored(): void
    {
        $product = Product::factory()->create(['price' => 1000000]);

        $this->post(route('orders.store'), $this->orderData([
            ['product_id' => $product->id, 'quantity' => 1, 'price' => 1],
        ]));

        $this->assertEquals(1000000, Order::sole()->total_amount);
    }

    public function test_order_validation(): void
    {
        $product = Product::factory()->create();
        $stopped = Product::factory()->stopped()->create();
        $line = [['product_id' => $product->id, 'quantity' => 1]];

        $this->post(route('orders.store'), $this->orderData([]))->assertSessionHasErrors('items');
        $this->post(route('orders.store'), $this->orderData($line, ['customer_name' => '']))->assertSessionHasErrors('customer_name');
        $this->post(route('orders.store'), $this->orderData($line, ['customer_phone' => '12345']))->assertSessionHasErrors('customer_phone');
        $this->post(route('orders.store'), $this->orderData([['product_id' => $stopped->id, 'quantity' => 1]]))->assertSessionHasErrors('items.0.product_id');
        $this->post(route('orders.store'), $this->orderData([['product_id' => $product->id, 'quantity' => 0]]))->assertSessionHasErrors('items.0.quantity');
        $this->post(route('orders.store'), $this->orderData([
            ['product_id' => $product->id, 'quantity' => 1],
            ['product_id' => $product->id, 'quantity' => 2],
        ]))->assertSessionHasErrors('items.1.product_id');

        $this->assertDatabaseCount('orders', 0);
    }

    // ---------- Phân quyền xem đơn ----------

    public function test_sales_staff_only_sees_own_orders(): void
    {
        Order::factory()->for($this->staff)->create(['customer_name' => 'Khách của tôi']);
        Order::factory()->create(['customer_name' => 'Khách của người khác']);

        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Khách của tôi')
            ->assertDontSee('Khách của người khác');
    }

    public function test_manager_sees_all_orders_and_can_filter_by_employee(): void
    {
        Order::factory()->for($this->staff)->create(['customer_name' => 'Khách của Bán']);
        Order::factory()->create(['customer_name' => 'Khách của người khác']);
        $this->actingAs(Employee::factory()->manager()->create());

        $this->get(route('orders.index'))->assertSee('Khách của Bán')->assertSee('Khách của người khác');
        $this->get(route('orders.index', ['employee_id' => $this->staff->id]))
            ->assertSee('Khách của Bán')
            ->assertDontSee('Khách của người khác');
    }

    public function test_orders_can_be_filtered_by_status(): void
    {
        Order::factory()->for($this->staff)->create(['customer_name' => 'Đơn chờ']);
        Order::factory()->for($this->staff)->status(OrderStatus::Completed)->create(['customer_name' => 'Đơn xong']);

        $this->get(route('orders.index', ['status' => OrderStatus::Completed->value]))
            ->assertSee('Đơn xong')
            ->assertDontSee('Đơn chờ');
    }

    public function test_sales_staff_cannot_view_other_staff_order(): void
    {
        $other = Order::factory()->create();

        $this->get(route('orders.show', $other))->assertForbidden();
    }

    public function test_manager_can_view_any_order(): void
    {
        $order = Order::factory()->for($this->staff)->create();
        $this->actingAs(Employee::factory()->manager()->create());

        $this->get(route('orders.show', $order))->assertOk()->assertSee('Trần Thị Bán');
    }

    // ---------- Cập nhật trạng thái ----------

    public function test_order_status_follows_allowed_flow(): void
    {
        $order = Order::factory()->for($this->staff)->create();

        $this->patch(route('orders.update-status', $order), ['status' => 'processing'])->assertSessionHas('success');
        $this->assertSame(OrderStatus::Processing, $order->fresh()->status);

        $this->patch(route('orders.update-status', $order), ['status' => 'completed'])->assertSessionHas('success');
        $this->assertSame(OrderStatus::Completed, $order->fresh()->status);
    }

    public function test_order_cannot_skip_or_go_back_a_step(): void
    {
        $order = Order::factory()->for($this->staff)->create();

        $this->patch(route('orders.update-status', $order), ['status' => 'completed'])->assertSessionHas('error');
        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);

        $this->patch(route('orders.update-status', $order), ['status' => 'unknown'])->assertSessionHasErrors('status');
    }

    public function test_completed_or_cancelled_order_cannot_change_status(): void
    {
        $completed = Order::factory()->for($this->staff)->status(OrderStatus::Completed)->create();
        $cancelled = Order::factory()->for($this->staff)->status(OrderStatus::Cancelled)->create();

        $this->patch(route('orders.update-status', $completed), ['status' => 'cancelled'])->assertForbidden();
        $this->patch(route('orders.update-status', $cancelled), ['status' => 'pending'])->assertForbidden();
    }

    public function test_sales_staff_cannot_update_other_staff_order(): void
    {
        $other = Order::factory()->create();

        $this->patch(route('orders.update-status', $other), ['status' => 'processing'])->assertForbidden();
        $this->assertSame(OrderStatus::Pending, $other->fresh()->status);
    }

    // ---------- Xóa đơn ----------

    public function test_sales_staff_can_delete_own_pending_order(): void
    {
        $order = Order::factory()->for($this->staff)->create();

        $this->delete(route('orders.destroy', $order))->assertRedirect(route('orders.index'));

        $this->assertModelMissing($order);
    }

    public function test_sales_staff_cannot_delete_processed_or_other_orders(): void
    {
        $processing = Order::factory()->for($this->staff)->status(OrderStatus::Processing)->create();
        $other = Order::factory()->create();

        $this->delete(route('orders.destroy', $processing))->assertForbidden();
        $this->delete(route('orders.destroy', $other))->assertForbidden();

        $this->assertModelExists($processing);
        $this->assertModelExists($other);
    }

    public function test_manager_can_delete_any_order_and_its_items(): void
    {
        $product = Product::factory()->create();
        $order = Order::factory()->status(OrderStatus::Completed)->create();
        $order->items()->create(['product_id' => $product->id, 'quantity' => 1, 'price' => $product->price]);

        $this->actingAs(Employee::factory()->manager()->create())
            ->delete(route('orders.destroy', $order))
            ->assertRedirect(route('orders.index'));

        $this->assertModelMissing($order);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertModelExists($product);
    }
}
