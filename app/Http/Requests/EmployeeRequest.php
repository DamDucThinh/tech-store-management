<?php

namespace App\Http\Requests;

use App\Enums\EmployeeRole;
use App\Enums\EmployeeStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employee = $this->route('employee');

        return [
            // Tài khoản (bảng employees)
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('employees', 'username')->ignore($employee),
            ],
            // Thêm mới: bắt buộc nhập mật khẩu. Sửa: để trống thì giữ mật khẩu cũ.
            'password' => [$employee ? 'nullable' : 'required', 'confirmed', Password::min(6)],
            'role' => ['required', Rule::enum(EmployeeRole::class)],
            'status' => ['required', Rule::enum(EmployeeStatus::class)],

            // Thông tin nhân viên (bảng employee_profiles)
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['nullable', 'regex:/^0[0-9]{9,10}$/'],
            'address' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'Tên đăng nhập chỉ gồm chữ không dấu, chữ số và các ký tự . _ -',
            'phone.regex' => 'Số điện thoại gồm 10–11 chữ số và bắt đầu bằng số 0.',
        ];
    }
}
