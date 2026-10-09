<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Employee;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all()->keyBy('username');
        $products = Product::all()->keyBy('name');

        // [nhân viên, khách hàng, SĐT, số ngày trước hôm nay, trạng thái, [[sản phẩm, số lượng], ...]]
        $orders = [
            ['staff', 'Phạm Thu Trang', '0934567890', 40, OrderStatus::Completed, [['iPhone 16 128GB', 1], ['Sạc nhanh Anker 20W', 1]]],
            ['tuan.pham', 'Đỗ Quang Huy', '0945678901', 35, OrderStatus::Completed, [['Dell Inspiron 15 3530', 1], ['Chuột không dây Logitech M331', 1]]],
            ['hang.le', 'Vũ Thị Lan', '0956789012', 28, OrderStatus::Completed, [['Xiaomi Redmi Note 14', 2], ['Cáp USB-C to USB-C 1m', 2]]],
            ['manager', 'Công ty TNHH An Phát', '02437654321', 21, OrderStatus::Completed, [['MacBook Air M3 13 inch', 3], ['Máy in Canon LBP 2900', 1]]],
            ['staff', 'Ngô Bảo Châu', '0966778899', 18, OrderStatus::Cancelled, [['Tai nghe AirPods Pro 2', 1]]],
            ['hang.le', 'Bùi Anh Tuấn', '0977889900', 1, OrderStatus::Completed, [['Samsung Galaxy S25 256GB', 1], ['Bàn phím cơ Akko 3087', 1]]],
            ['staff', 'Hoàng Mai Anh', '0988990011', 0, OrderStatus::Completed, [['Tai nghe AirPods Pro 2', 1], ['Cáp USB-C to USB-C 1m', 1]]],
            ['hang.le', 'Trịnh Văn Nam', '0911223344', 5, OrderStatus::Processing, [['ASUS TUF Gaming F15', 1], ['Chuột không dây Logitech M331', 2]]],
            ['staff', 'Lý Thị Hoa', '0922334455', 3, OrderStatus::Processing, [['iPhone 16 128GB', 2], ['Sạc nhanh Anker 20W', 2]]],
            ['staff', 'Đặng Gia Bảo', '0933445566', 1, OrderStatus::Pending, [['Xiaomi Redmi Note 14', 1], ['Bàn phím cơ Akko 3087', 1]]],
            ['hang.le', 'Mai Phương Thảo', '0944556677', 0, OrderStatus::Pending, [['Máy in Canon LBP 2900', 1]]],
            ['staff', 'Cao Minh Đức', '0955667788', 0, OrderStatus::Pending, [['Samsung Galaxy S25 256GB', 1], ['Cáp USB-C to USB-C 1m', 1]]],
        ];

        foreach ($orders as [$username, $customer, $phone, $daysAgo, $status, $lines]) {
            // Lùi thêm vài giờ để ngày tạo không bao giờ rơi vào tương lai.
            $createdAt = now()->subDays($daysAgo)->subMinutes(rand(30, 360));

            // forceCreate để ghi được created_at theo ngày mẫu.
            $order = $employees[$username]->orders()->forceCreate([
                'customer_name' => $customer,
                'customer_phone' => $phone,
                'total_amount' => 0,
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $total = 0;

            foreach ($lines as [$productName, $quantity]) {
                $product = $products[$productName];
                $order->items()->create(['product_id' => $product->id, 'quantity' => $quantity, 'price' => $product->price]);
                $total += $product->price * $quantity;
            }

            $order->forceFill(['total_amount' => $total])->saveQuietly();
        }
    }
}
