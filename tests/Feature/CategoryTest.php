<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Employee;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(Employee::factory()->manager()->create());
    }

    public function test_sales_staff_cannot_manage_categories(): void
    {
        $category = Category::factory()->create();
        $this->actingAs(Employee::factory()->create());

        $this->get(route('categories.index'))->assertForbidden();
        $this->post(route('categories.store'), ['name' => 'Mới'])->assertForbidden();
        $this->delete(route('categories.destroy', $category))->assertForbidden();
    }

    public function test_category_list_is_displayed(): void
    {
        Category::factory()->create(['name' => 'Điện thoại']);

        $this->get(route('categories.index'))->assertOk()->assertSee('Điện thoại');
    }

    public function test_category_can_be_created(): void
    {
        $this->post(route('categories.store'), ['name' => 'Thiết bị văn phòng'])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Thiết bị văn phòng']);
    }

    public function test_category_name_is_required_and_unique(): void
    {
        Category::factory()->create(['name' => 'Máy tính']);

        $this->post(route('categories.store'), ['name' => ''])->assertSessionHasErrors('name');
        $this->post(route('categories.store'), ['name' => 'Máy tính'])->assertSessionHasErrors('name');
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_category_can_be_updated_and_keep_its_own_name(): void
    {
        $category = Category::factory()->create(['name' => 'Máy tính']);

        $this->put(route('categories.update', $category), ['name' => 'Máy tính'])->assertSessionHasNoErrors();
        $this->put(route('categories.update', $category), ['name' => 'Laptop'])->assertRedirect(route('categories.index'));

        $this->assertSame('Laptop', $category->fresh()->name);
    }

    public function test_empty_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $this->delete(route('categories.destroy', $category))->assertRedirect(route('categories.index'));

        $this->assertModelMissing($category);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::factory()->has(Product::factory()->count(2))->create();

        $this->from(route('categories.index'))
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($category);
    }
}
