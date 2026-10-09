<?php

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation phía backend cho thêm và sửa sản phẩm.
 *
 * Form đã có validation HTML5 (required, type=number, min=0, accept=image/*)
 * để báo lỗi sớm, nhưng request vẫn có thể được gửi thẳng bằng Postman,
 * nên backend phải kiểm tra lại. Nếu không hợp lệ, Laravel tự quay lại form
 * kèm lỗi và không gọi tới ProductController@store/update.
 */
class ProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'status' => ['required', Rule::enum(ProductStatus::class)],
        ];
    }
}
