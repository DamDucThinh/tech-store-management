<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Quan hệ 1-N: một category có nhiều product.
     * restrictOnDelete: database từ chối xóa category khi còn product tham chiếu.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->decimal('price', 12, 2);
            $table->text('description')->nullable();
            $table->string('image')->nullable(); // đường dẫn file trong storage/app/public
            $table->string('status', 20)->default('selling'); // selling | stopped
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
