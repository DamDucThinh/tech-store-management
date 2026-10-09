<div class="field">
    <label for="name" class="label">Tên danh mục <span class="req">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
           @class(['input', 'is-invalid' => $errors->has('name')]) required maxlength="100"
           placeholder="Ví dụ: Thiết bị văn phòng">
    @error('name')
        <div class="field-error">{{ $message }}</div>
    @enderror
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Lưu</button>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Hủy</a>
</div>
