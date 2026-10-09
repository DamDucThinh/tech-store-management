<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Employee;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * total_amount để 0, test hoặc seeder tự tạo dòng hàng rồi tính lại.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->numerify('09########'),
            'total_amount' => 0,
            'status' => OrderStatus::Pending,
        ];
    }

    public function status(OrderStatus $status): static
    {
        return $this->state(fn (array $attributes) => ['status' => $status]);
    }
}
