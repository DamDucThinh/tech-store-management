<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Mở thử mọi trang với dữ liệu mẫu, cho cả Quản lý và Nhân viên bán hàng.
 */
class PageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
    }

    public function test_manager_can_open_every_page(): void
    {
        $manager = Employee::where('username', 'manager')->sole();
        $this->actingAs($manager);

        $product = Product::first();
        $order = Order::first();

        foreach ([
            route('dashboard'),
            route('orders.index'), route('orders.create'), route('orders.show', $order),
            route('products.index'), route('products.create'), route('products.show', $product), route('products.edit', $product),
            route('categories.index'), route('categories.create'), route('categories.edit', Category::first()),
            route('employees.index'), route('employees.create'), route('employees.show', $manager), route('employees.edit', $manager),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_sales_staff_sees_own_pages_and_no_manager_menu(): void
    {
        $staff = Employee::where('username', 'staff')->sole();
        $this->actingAs($staff);

        $this->get(route('dashboard'))->assertOk()->assertDontSee('Nhân viên</a>', false);
        $this->get(route('orders.index'))->assertOk()->assertSee('Đơn hàng của tôi');
        $this->get(route('orders.create'))->assertOk();
        $this->get(route('orders.show', $staff->orders()->first()))->assertOk();
        $this->get(route('products.index'))->assertOk();
        $this->get(route('products.show', Product::first()))->assertOk();
    }
}
