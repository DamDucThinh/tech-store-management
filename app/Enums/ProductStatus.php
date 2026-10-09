<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Selling = 'selling';
    case Stopped = 'stopped';

    public function label(): string
    {
        return match ($this) {
            self::Selling => 'Đang bán',
            self::Stopped => 'Ngừng bán',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Selling => 'success',
            self::Stopped => 'muted',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_column(array_map(fn (self $case) => [$case->value, $case->label()], self::cases()), 1, 0);
    }
}
