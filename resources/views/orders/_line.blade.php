<tr class="order-line">
    <td data-label="Sản phẩm">
        <select name="items[{{ $index }}][product_id]" aria-label="Sản phẩm"
                @class(['select', 'product-select', 'is-invalid' => $errors->has("items.$index.product_id")]) required>
            <option value="">-- Chọn sản phẩm --</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" data-price="{{ (float) $product->price }}"
                        @selected((string) ($item['product_id'] ?? '') === (string) $product->id)>
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
        @error("items.$index.product_id")
            <div class="field-error">{{ $message }}</div>
        @enderror
    </td>
    <td data-label="Đơn giá" class="text-right nowrap unit-price">—</td>
    <td data-label="Số lượng">
        <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" aria-label="Số lượng"
               @class(['input', 'qty-input', 'is-invalid' => $errors->has("items.$index.quantity")]) required min="1" max="1000" step="1">
        @error("items.$index.quantity")
            <div class="field-error">{{ $message }}</div>
        @enderror
    </td>
    <td data-label="Thành tiền" class="text-right nowrap strong line-total">—</td>
    <td class="text-right">
        <button type="button" class="btn btn-danger btn-sm remove-line" aria-label="Xóa dòng">Xóa</button>
    </td>
</tr>
