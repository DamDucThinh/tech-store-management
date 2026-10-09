<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Thứ tự quan trọng: bảng được tham chiếu (employees, categories) phải có
     * dữ liệu trước bảng tham chiếu tới nó (products, orders, order_items).
     */
    public function run(): void
    {
        $this->call([
            EmployeeSeeder::class,
            CategoryProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
