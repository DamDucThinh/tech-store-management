<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Kiểm tra các relationship Eloquent và ràng buộc Foreign Key ở tầng database.
 */
class RelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_has_one_profile_and_many_orders(): void
    {
        $employee = Employee::factory()->withProfile(['name' => 'Trần Thị Bán'])->has(Order::factory()->count(2))->create();

        $this->assertSame('Trần Thị Bán', $employee->profile->name);
        $this->assertCount(2, $employee->orders);
        $this->assertTrue($employee->profile->employee->is($employee));
        $this->assertTrue($employee->orders->first()->employee->is($employee));
    }

    public function test_employee_can_only_have_one_profile(): void
    {
        $employee = Employee::factory()->withProfile()->create();

        $this->expectException(QueryException::class);
        $employee->profile()->create(['name' => 'Profile thứ hai']);
    }

    public function test_category_has_many_products_and_product_belongs_to_category(): void
    {
        $category = Category::factory()->has(Product::factory()->count(3))->create();

        $this->assertCount(3, $category->products);
        $this->assertTrue($category->products->first()->category->is($category));
    }

    public function test_order_and_product_are_many_to_many_through_order_items(): void
    {
        $order = Order::factory()->create();
        [$first, $second] = Product::factory()->count(2)->create();

        $order->items()->create(['product_id' => $first->id, 'quantity' => 2, 'price' => 100000]);
        $order->items()->create(['product_id' => $second->id, 'quantity' => 1, 'price' => 50000]);

        $this->assertCount(2, $order->products);
        $this->assertSame(2, (int) $order->products->find($first->id)->pivot->quantity);
        $this->assertTrue($first->orders->first()->is($order));
        $this->assertCount(1, $first->orderItems);
    }

    public function test_database_rejects_deleting_category_that_has_products(): void
    {
        $category = Category::factory()->has(Product::factory())->create();

        $this->expectException(QueryException::class);
        $category->delete();
    }

    public function test_database_rejects_deleting_product_used_in_order_items(): void
    {
        $product = Product::factory()->create();
        Order::factory()->create()->items()->create(['product_id' => $product->id, 'quantity' => 1, 'price' => 1000]);

        $this->expectException(QueryException::class);
        $product->delete();
    }

    public function test_database_rejects_order_item_with_unknown_product(): void
    {
        $order = Order::factory()->create();

        $this->expectException(QueryException::class);
        $order->items()->create(['product_id' => 999999, 'quantity' => 1, 'price' => 1000]);
    }
}
