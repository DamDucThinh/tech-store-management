<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        // [danh mục, màu ảnh minh họa, [[tên, giá, trạng thái, mô tả], ...]]
        $catalog = [
            ['Điện thoại', '#2563eb', [
                ['iPhone 16 128GB', 22990000, ProductStatus::Selling, 'Chip A18, màn hình 6.1 inch, camera 48MP.'],
                ['Samsung Galaxy S25 256GB', 21490000, ProductStatus::Selling, 'Màn hình Dynamic AMOLED 6.2 inch, pin 4000 mAh.'],
                ['Xiaomi Redmi Note 14', 5990000, ProductStatus::Selling, 'Pin 5500 mAh, sạc nhanh 33W.'],
                ['OPPO A79 5G', 6490000, ProductStatus::Stopped, 'Mẫu cũ, đã ngừng nhập hàng.'],
            ]],
            ['Máy tính', '#7c3aed', [
                ['MacBook Air M3 13 inch', 27990000, ProductStatus::Selling, 'Chip Apple M3, RAM 8GB, SSD 256GB.'],
                ['Dell Inspiron 15 3530', 15490000, ProductStatus::Selling, 'Core i5 thế hệ 13, RAM 8GB, SSD 512GB.'],
                ['ASUS TUF Gaming F15', 19990000, ProductStatus::Selling, 'Core i7, RTX 4050, màn hình 144Hz.'],
            ]],
            ['Phụ kiện', '#0d9488', [
                ['Sạc nhanh Anker 20W', 390000, ProductStatus::Selling, 'Cổng USB-C, hỗ trợ Power Delivery.'],
                ['Cáp USB-C to USB-C 1m', 150000, ProductStatus::Selling, 'Bọc dù, hỗ trợ sạc 60W.'],
                ['Tai nghe AirPods Pro 2', 5990000, ProductStatus::Selling, 'Chống ồn chủ động, hộp sạc USB-C.'],
                ['Ốp lưng iPhone 14 trong suốt', 120000, ProductStatus::Stopped, 'Không còn hàng.'],
            ]],
            ['Thiết bị văn phòng', '#ea580c', [
                ['Máy in Canon LBP 2900', 3490000, ProductStatus::Selling, 'Máy in laser đen trắng, khổ A4.'],
                ['Chuột không dây Logitech M331', 350000, ProductStatus::Selling, 'Click yên lặng, pin 24 tháng.'],
                ['Bàn phím cơ Akko 3087', 1290000, ProductStatus::Selling, 'Layout TKL 87 phím, switch Akko.'],
            ]],
            ['Thiết bị mạng', '#64748b', []],
        ];

        foreach ($catalog as [$categoryName, $color, $products]) {
            $category = Category::create(['name' => $categoryName]);

            foreach ($products as [$name, $price, $status, $description]) {
                $category->products()->create([
                    'name' => $name,
                    'price' => $price,
                    'status' => $status,
                    'description' => $description,
                    'image' => $this->makeImage($name, $categoryName, $color),
                ]);
            }
        }
    }

    /**
     * Tạo ảnh minh họa dạng SVG cho sản phẩm mẫu, lưu vào storage/app/public/products.
     */
    private function makeImage(string $name, string $category, string $color): string
    {
        $path = 'products/seed-'.Str::slug($name).'.svg';
        $lines = $this->wrap($name, 16);
        $text = '';

        foreach ($lines as $i => $line) {
            $y = 330 + $i * 46 - (count($lines) - 1) * 23;
            $text .= '<text x="300" y="'.$y.'" font-size="38" font-weight="700" fill="#fff" text-anchor="middle">'.e($line).'</text>';
        }

        $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600">
            <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{$color}"/><stop offset="1" stop-color="{$color}" stop-opacity=".7"/></linearGradient></defs>
            <rect width="600" height="600" fill="url(#g)"/>
            <circle cx="300" cy="190" r="70" fill="#fff" fill-opacity=".18"/>
            <text x="300" y="214" font-size="72" font-weight="800" fill="#fff" text-anchor="middle" font-family="Arial, sans-serif">{$this->initial($name)}</text>
            <g font-family="Arial, sans-serif">{$text}</g>
            <text x="300" y="540" font-size="24" fill="#fff" fill-opacity=".8" text-anchor="middle" font-family="Arial, sans-serif">{$this->escape($category)}</text>
        </svg>
        SVG;

        Storage::disk('public')->put($path, $svg);

        return $path;
    }

    /**
     * @return list<string>
     */
    private function wrap(string $text, int $width): array
    {
        $lines = [];
        $current = '';

        foreach (explode(' ', $text) as $word) {
            if ($current !== '' && mb_strlen($current.' '.$word) > $width) {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = trim($current.' '.$word);
            }
        }

        $lines[] = $current;

        return $lines;
    }

    private function initial(string $name): string
    {
        return e(mb_strtoupper(mb_substr($name, 0, 1)));
    }

    private function escape(string $value): string
    {
        return e($value);
    }
}
