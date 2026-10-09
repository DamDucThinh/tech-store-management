<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Employee;
use App\Models\Order;

/**
 * Phân quyền trên từng đơn hàng.
 * Laravel tự tìm policy này cho model Order (theo quy ước tên App\Policies\OrderPolicy).
 */
class OrderPolicy
{
    /**
     * Quản lý xem được mọi đơn, nhân viên bán hàng chỉ xem đơn mình tạo.
     */
    public function view(Employee $employee, Order $order): bool
    {
        return $employee->isManager() || $order->isOwnedBy($employee);
    }

    /**
     * Đơn đã Hoàn thành hoặc Đã hủy thì không đổi trạng thái được nữa.
     */
    public function updateStatus(Employee $employee, Order $order): bool
    {
        return $this->view($employee, $order) && ! $order->status->isFinal();
    }

    /**
     * Quản lý xóa được mọi đơn. Nhân viên bán hàng chỉ xóa được đơn của mình
     * khi đơn còn ở trạng thái Chờ xử lý.
     */
    public function delete(Employee $employee, Order $order): bool
    {
        if ($employee->isManager()) {
            return true;
        }

        return $order->isOwnedBy($employee) && $order->status === OrderStatus::Pending;
    }
}
