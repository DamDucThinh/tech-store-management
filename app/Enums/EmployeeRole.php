<?php

namespace App\Enums;

enum EmployeeRole: string
{
    case Manager = 'manager';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Manager => 'Quản lý',
            self::Staff => 'Nhân viên bán hàng',
        };
    }

    /**
     * @return array<string, string> [value => label] dùng cho thẻ <select>
     */
    public static function options(): array
    {
        return array_column(array_map(fn (self $case) => [$case->value, $case->label()], self::cases()), 1, 0);
    }
}
