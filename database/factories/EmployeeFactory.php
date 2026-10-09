<?php

namespace Database\Factories;

use App\Enums\EmployeeRole;
use App\Enums\EmployeeStatus;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => EmployeeRole::Staff,
            'status' => EmployeeStatus::Active,
        ];
    }

    public function manager(): static
    {
        return $this->state(fn (array $attributes) => ['role' => EmployeeRole::Manager]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['status' => EmployeeStatus::Inactive]);
    }

    /**
     * Tạo kèm profile (quan hệ 1-1).
     */
    public function withProfile(array $attributes = []): static
    {
        return $this->afterCreating(fn (Employee $employee) => $employee->profile()->create(array_merge([
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('09########'),
            'address' => fake()->address(),
        ], $attributes)));
    }
}
