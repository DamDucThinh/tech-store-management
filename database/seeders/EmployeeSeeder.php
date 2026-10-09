<?php

namespace Database\Seeders;

use App\Enums\EmployeeRole;
use App\Enums\EmployeeStatus;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Mật khẩu của mọi tài khoản demo là "password".
     */
    public function run(): void
    {
        $employees = [
            [
                ['username' => 'manager', 'role' => EmployeeRole::Manager, 'status' => EmployeeStatus::Active],
                ['name' => 'Nguyễn Văn Quản', 'email' => 'quan.nguyen@techstore.test', 'phone' => '0901234567', 'address' => '12 Láng Hạ, Đống Đa, Hà Nội'],
            ],
            [
                ['username' => 'staff', 'role' => EmployeeRole::Staff, 'status' => EmployeeStatus::Active],
                ['name' => 'Trần Thị Bán', 'email' => 'ban.tran@techstore.test', 'phone' => '0912345678', 'address' => '45 Cầu Giấy, Hà Nội'],
            ],
            [
                ['username' => 'hang.le', 'role' => EmployeeRole::Staff, 'status' => EmployeeStatus::Active],
                ['name' => 'Lê Minh Hàng', 'email' => 'hang.le@techstore.test', 'phone' => '0987654321', 'address' => '8 Nguyễn Trãi, Thanh Xuân, Hà Nội'],
            ],
            [
                ['username' => 'tuan.pham', 'role' => EmployeeRole::Staff, 'status' => EmployeeStatus::Inactive],
                ['name' => 'Phạm Anh Tuấn', 'email' => 'tuan.pham@techstore.test', 'phone' => '0976543210', 'address' => '20 Kim Mã, Ba Đình, Hà Nội'],
            ],
        ];

        foreach ($employees as [$account, $profile]) {
            $employee = Employee::create($account + ['password' => 'password']);
            $employee->profile()->create($profile);
        }
    }
}
