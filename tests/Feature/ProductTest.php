<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->actingAs(Employee::factory()->manager()->create());
        $this->category = Category::factory()->create(['name' => 'Điện thoại']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'iPhone 16',
            'category_id' => $this->category->id,
            'price' => 22990000,
            'description' => 'Hàng chính hãng',
            'status' => ProductStatus::Selling->value,
        ], $overrides);
    }

    public function test_sales_staff_can_view_but_not_change_products(): void
    {
        $product = Product::factory()->for($this->category)->create(['name' => 'Loa JBL Flip 6']);
        $this->actingAs(Employee::factory()->create());

        $this->get(route('products.index'))->assertOk()->assertSee('Loa JBL Flip 6')->assertDontSee('Thêm sản phẩm');
        $this->get(route('products.show', $product))->assertOk();
        $this->get(route('products.create'))->assertForbidden();
        $this->post(route('products.store'), $this->validData())->assertForbidden();
        $this->get(route('products.edit', $product))->assertForbidden();
        $this->put(route('products.update', $product), $this->validData())->assertForbidden();
        $this->delete(route('products.destroy', $product))->assertForbidden();
    }

    public function test_products_can_be_searched_by_name(): void
    {
        Product::factory()->for($this->category)->create(['name' => 'iPhone 16 128GB']);
        Product::factory()->for($this->category)->create(['name' => 'Samsung Galaxy S25']);

        $this->get(route('products.index', ['q' => 'iphone']))
            ->assertSee('iPhone 16 128GB')
            ->assertDontSee('Samsung Galaxy S25');
    }

    public function test_products_can_be_filtered_by_category_and_status(): void
    {
        $laptops = Category::factory()->create(['name' => 'Máy tính']);
        Product::factory()->for($this->category)->create(['name' => 'Điện thoại đang bán']);
        Product::factory()->for($this->category)->stopped()->create(['name' => 'Điện thoại ngừng bán']);
        Product::factory()->for($laptops)->create(['name' => 'Laptop đang bán']);

        $this->get(route('products.index', ['category_id' => $laptops->id]))
            ->assertSee('Laptop đang bán')
            ->assertDontSee('Điện thoại đang bán');

        $this->get(route('products.index', ['status' => ProductStatus::Stopped->value]))
            ->assertSee('Điện thoại ngừng bán')
            ->assertDontSee('Điện thoại đang bán')
            ->assertDontSee('Laptop đang bán');
    }

    public function test_product_can_be_created_with_image(): void
    {
        $this->post(route('products.store'), $this->validData(['image' => UploadedFile::fake()->image('iphone.jpg', 600, 600)]))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('success');

        $product = Product::sole();
        $this->assertSame('iPhone 16', $product->name);
        $this->assertSame(ProductStatus::Selling, $product->status);
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_product_can_be_created_without_image(): void
    {
        $this->post(route('products.store'), $this->validData())->assertSessionHasNoErrors();

        $this->assertNull(Product::sole()->image);
    }

    public function test_price_must_be_numeric(): void
    {
        $this->post(route('products.store'), $this->validData(['price' => 'abc']))
            ->assertSessionHasErrors(['price' => 'Giá phải là số.']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_price_cannot_be_negative(): void
    {
        $this->post(route('products.store'), $this->validData(['price' => -1000]))->assertSessionHasErrors('price');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post(route('products.store'), [])
            ->assertSessionHasErrors(['name', 'category_id', 'price', 'status']);
    }

    public function test_category_and_status_must_be_valid(): void
    {
        $this->post(route('products.store'), $this->validData(['category_id' => 9999]))->assertSessionHasErrors('category_id');
        $this->post(route('products.store'), $this->validData(['status' => 'deleted']))->assertSessionHasErrors('status');
    }

    public function test_image_must_be_an_image_file(): void
    {
        $this->post(route('products.store'), $this->validData(['image' => UploadedFile::fake()->create('virus.pdf', 100, 'application/pdf')]))
            ->assertSessionHasErrors('image');

        $this->post(route('products.store'), $this->validData(['image' => UploadedFile::fake()->image('big.jpg')->size(3000)]))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_detail_is_displayed(): void
    {
        $product = Product::factory()->for($this->category)->create(['name' => 'Loa JBL Flip 6', 'description' => 'Chống nước IP67']);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Loa JBL Flip 6')
            ->assertSee('Chống nước IP67')
            ->assertSee('Điện thoại');
    }

    public function test_product_can_be_updated_and_image_replaced(): void
    {
        $oldImage = UploadedFile::fake()->image('old.jpg')->store('products', 'public');
        $product = Product::factory()->for($this->category)->create(['image' => $oldImage]);

        $this->put(route('products.update', $product), $this->validData([
            'name' => 'Tên mới',
            'status' => ProductStatus::Stopped->value,
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]))->assertRedirect(route('products.show', $product));

        $product->refresh();
        $this->assertSame('Tên mới', $product->name);
        $this->assertSame(ProductStatus::Stopped, $product->status);
        $this->assertNotSame($oldImage, $product->image);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_update_without_new_image_keeps_old_image(): void
    {
        $image = UploadedFile::fake()->image('old.jpg')->store('products', 'public');
        $product = Product::factory()->for($this->category)->create(['image' => $image]);

        $this->put(route('products.update', $product), $this->validData());

        $this->assertSame($image, $product->fresh()->image);
        Storage::disk('public')->assertExists($image);
    }

    public function test_image_can_be_removed(): void
    {
        $image = UploadedFile::fake()->image('old.jpg')->store('products', 'public');
        $product = Product::factory()->for($this->category)->create(['image' => $image]);

        $this->put(route('products.update', $product), $this->validData(['remove_image' => '1']));

        $this->assertNull($product->fresh()->image);
        Storage::disk('public')->assertMissing($image);
    }

    public function test_unused_product_and_its_image_are_deleted(): void
    {
        $image = UploadedFile::fake()->image('old.jpg')->store('products', 'public');
        $product = Product::factory()->for($this->category)->create(['image' => $image]);

        $this->delete(route('products.destroy', $product))->assertRedirect(route('products.index'));

        $this->assertModelMissing($product);
        Storage::disk('public')->assertMissing($image);
    }

    public function test_product_used_in_order_items_cannot_be_deleted(): void
    {
        $product = Product::factory()->for($this->category)->create();
        Order::factory()->create()->items()->create(['product_id' => $product->id, 'quantity' => 1, 'price' => $product->price]);

        $this->from(route('products.index'))
            ->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'))
            ->assertSessionHas('error');

        $this->assertModelExists($product);
    }
}
