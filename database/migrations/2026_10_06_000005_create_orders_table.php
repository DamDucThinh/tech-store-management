<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Quan hệ 1-N: một employee tạo nhiều order. Ngày đặt hàng là created_at.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->restrictOnDelete();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 20);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('status', 20)->default('pending'); // pending | processing | completed | cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
