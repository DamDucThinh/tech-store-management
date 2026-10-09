<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'price' => fake()->numberBetween(10, 5000) * 1000,
            'description' => fake()->sentence(),
            'image' => null,
            'status' => ProductStatus::Selling,
        ];
    }

    public function stopped(): static
    {
        return $this->state(fn (array $attributes) => ['status' => ProductStatus::Stopped]);
    }
}
