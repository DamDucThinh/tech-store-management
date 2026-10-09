<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'phone', 'address', 'avatar'])]
class EmployeeProfile extends Model
{
    /**
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    /**
     * Chữ cái đầu của tên, hiển thị khi chưa có avatar. Ví dụ "Trần Thị Bán" -> "TB".
     */
    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->name)) ?: [];
        $first = Str::substr($words[0] ?? '', 0, 1);
        $last = count($words) > 1 ? Str::substr(end($words), 0, 1) : '';

        return Str::upper($first.$last);
    }
}
