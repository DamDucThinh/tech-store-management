@if ($categories->isEmpty())
    <div class="alert alert-error">
        <span>Chưa có danh mục nào. <a href="{{ route('categories.create') }}">Thêm danh mục</a> trước khi thêm sản phẩm.</span>
    </div>
@endif

<div class="form-grid">
    <div class="field">
        <label for="name" class="label">Tên sản phẩm <span class="req">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
               @class(['input', 'is-invalid' => $errors->has('name')]) required maxlength="150">
        @error('name')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field field-half">
        <label for="category_id" class="label">Danh mục <span class="req">*</span></label>
        <select id="category_id" name="category_id" @class(['select', 'is-invalid' => $errors->has('category_id')]) required>
            <option value="">-- Chọn danh mục --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $product->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field field-half">
        <label for="price" class="label">Giá bán (₫) <span class="req">*</span></label>
        <input type="number" id="price" name="price" value="{{ old('price', $product->exists ? (float) $product->price : '') }}"
               @class(['input', 'is-invalid' => $errors->has('price')]) required min="0" step="any" placeholder="Ví dụ: 1500000">
        @error('price')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field">
        <span class="label">Trạng thái <span class="req">*</span></span>
        <div class="radio-group">
            @foreach (\App\Enums\ProductStatus::cases() as $status)
                <label class="choice">
                    <input type="radio" name="status" value="{{ $status->value }}" required
                           @checked(old('status', $product->status?->value ?? \App\Enums\ProductStatus::Selling->value) === $status->value)>
                    {{ $status->label() }}
                </label>
            @endforeach
        </div>
        @error('status')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field">
        <label for="image" class="label">Hình ảnh</label>
        <div class="upload">
            <div class="upload-preview" id="image-preview">
                @if ($product->imageUrl())
                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}">
                @else
                    Chưa có ảnh
                @endif
            </div>
            <div class="upload-body">
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                       @class(['input', 'is-invalid' => $errors->has('image')]) data-preview="#image-preview">
                <div class="hint">JPG, PNG hoặc WEBP, tối đa 2 MB.</div>
                @if ($product->image)
                    <label class="choice">
                        <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                        Xóa ảnh hiện tại
                    </label>
                @endif
                @error('image')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="field">
        <label for="description" class="label">Mô tả</label>
        <textarea id="description" name="description" rows="4" maxlength="2000"
                  @class(['textarea', 'is-invalid' => $errors->has('description')])>{{ old('description', $product->description) }}</textarea>
        @error('description')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
    <a href="{{ $product->exists ? route('products.show', $product) : route('products.index') }}" class="btn btn-secondary">Hủy</a>
</div>
