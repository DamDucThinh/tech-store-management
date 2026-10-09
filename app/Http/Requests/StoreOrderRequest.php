<?php

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'regex:/^0[0-9]{9,10}$/'],

            'items' => ['required', 'array', 'min:1'],
            // Mỗi sản phẩm chỉ xuất hiện một lần trong đơn, và phải đang bán.
            'items.*.product_id' => [
                'required',
                'distinct',
                Rule::exists('products', 'id')->where('status', ProductStatus::Selling->value),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'Số điện thoại gồm 10–11 chữ số và bắt đầu bằng số 0.',
            'items.required' => 'Đơn hàng phải có ít nhất một sản phẩm.',
            'items.min' => 'Đơn hàng phải có ít nhất một sản phẩm.',
            'items.*.product_id.distinct' => 'Sản phẩm bị chọn trùng, hãy gộp vào một dòng và tăng số lượng.',
            'items.*.product_id.exists' => 'Sản phẩm không tồn tại hoặc đã ngừng bán.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'items.*.product_id' => 'sản phẩm',
            'items.*.quantity' => 'số lượng',
        ];
    }
}
