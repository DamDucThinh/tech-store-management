<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Bảng trung gian cho quan hệ N-N giữa orders và products.
     * Lưu thêm quantity và price tại thời điểm đặt hàng, nên khi giá sản phẩm
     * thay đổi về sau thì lịch sử đơn hàng vẫn giữ đúng giá đã bán.
     *
     * - order_id: cascadeOnDelete, xóa order thì xóa luôn các dòng hàng.
     * - product_id: restrictOnDelete, không cho xóa product đã nằm trong đơn hàng.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price', 12, 2);
            $table->timestamps();

            $table->unique(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
