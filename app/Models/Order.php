<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['employee_id', 'customer_name', 'customer_phone', 'total_amount', 'status'])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'status' => OrderStatus::class,
        ];
    }

    /**
     * N-1: order do một employee tạo.
     *
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * 1-N: order có nhiều dòng hàng.
     *
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * N-N: order có nhiều product, thông qua bảng order_items.
     *
     * @return BelongsToMany<Product, $this>
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot(['quantity', 'price'])
            ->withTimestamps();
    }

    /**
     * Phân quyền dữ liệu: quản lý thấy mọi đơn, nhân viên bán hàng chỉ thấy đơn mình tạo.
     *
     * @param  Builder<Order>  $query
     */
    public function scopeVisibleTo(Builder $query, Employee $employee): void
    {
        if (! $employee->isManager()) {
            $query->where('employee_id', $employee->id);
        }
    }

    public function isOwnedBy(Employee $employee): bool
    {
        return (int) $this->employee_id === (int) $employee->id;
    }
}
