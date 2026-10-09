@php
    $profile = $employee->profile;
    $isSelf = $employee->exists && $employee->is(auth()->user());
@endphp

<h2 class="form-section-title">Thông tin tài khoản</h2>
<div class="form-grid">
    <div class="field field-half">
        <label for="username" class="label">Username <span class="req">*</span></label>
        <input type="text" id="username" name="username" value="{{ old('username', $employee->username) }}"
               @class(['input', 'is-invalid' => $errors->has('username')])
               required maxlength="50" pattern="[A-Za-z0-9._\-]+" autocomplete="off">
        <div class="hint">Chữ không dấu, chữ số và các ký tự . _ -</div>
        @error('username')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field field-half">
        <label for="role" class="label">Vai trò <span class="req">*</span></label>
        @if ($isSelf)
            <input type="hidden" name="role" value="{{ $employee->role->value }}">
            <input type="text" id="role" class="input" value="{{ $employee->role->label() }}" disabled>
        @else
            <select id="role" name="role" @class(['select', 'is-invalid' => $errors->has('role')]) required>
                @foreach (\App\Enums\EmployeeRole::cases() as $role)
                    <option value="{{ $role->value }}" @selected(old('role', $employee->role?->value ?? \App\Enums\EmployeeRole::Staff->value) === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
        @endif
        @error('role')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field field-half">
        <label for="password" class="label">
            Password @unless ($employee->exists)<span class="req">*</span>@endunless
        </label>
        <input type="password" id="password" name="password" autocomplete="new-password" minlength="6"
               @class(['input', 'is-invalid' => $errors->has('password')]) @required(! $employee->exists)>
        <div class="hint">{{ $employee->exists ? 'Để trống nếu không đổi mật khẩu.' : 'Tối thiểu 6 ký tự.' }}</div>
        @error('password')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field field-half">
        <label for="password_confirmation" class="label">Nhập lại password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="input">
    </div>

    <div class="field">
        <span class="label">Status <span class="req">*</span></span>
        @if ($isSelf)
            <input type="hidden" name="status" value="{{ $employee->status->value }}">
            @include('partials.badge', ['status' => $employee->status])
            <div class="hint">Không thể tự đổi vai trò hoặc tự khóa tài khoản đang đăng nhập.</div>
        @else
            <div class="radio-group">
                @foreach (\App\Enums\EmployeeStatus::cases() as $status)
                    <label class="choice">
                        <input type="radio" name="status" value="{{ $status->value }}" required
                               @checked(old('status', $employee->status?->value ?? \App\Enums\EmployeeStatus::Active->value) === $status->value)>
                        {{ $status->label() }}
                    </label>
                @endforeach
            </div>
            <div class="hint">Tài khoản "Đã khóa" không đăng nhập được.</div>
        @endif
        @error('status')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<hr class="form-divider">

<h2 class="form-section-title">Thông tin nhân viên</h2>
<div class="form-grid">
    <div class="field">
        <label for="avatar" class="label">Avatar</label>
        <div class="upload">
            <div class="upload-preview is-round" id="avatar-preview">
                @if ($profile?->avatarUrl())
                    <img src="{{ $profile->avatarUrl() }}" alt="{{ $profile->name }}">
                @else
                    Chưa có ảnh
                @endif
            </div>
            <div class="upload-body">
                <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp"
                       @class(['input', 'is-invalid' => $errors->has('avatar')]) data-preview="#avatar-preview">
                <div class="hint">JPG, PNG hoặc WEBP, tối đa 2 MB.</div>
                @if ($profile?->avatar)
                    <label class="choice">
                        <input type="checkbox" name="remove_avatar" value="1" @checked(old('remove_avatar'))>
                        Xóa avatar hiện tại
                    </label>
                @endif
                @error('avatar')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="field field-half">
        <label for="name" class="label">Họ tên <span class="req">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name', $profile?->name) }}"
               @class(['input', 'is-invalid' => $errors->has('name')]) required maxlength="100">
        @error('name')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field field-half">
        <label for="email" class="label">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $profile?->email) }}"
               @class(['input', 'is-invalid' => $errors->has('email')]) maxlength="100">
        @error('email')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field field-half">
        <label for="phone" class="label">Số điện thoại</label>
        <input type="tel" id="phone" name="phone" value="{{ old('phone', $profile?->phone) }}"
               @class(['input', 'is-invalid' => $errors->has('phone')]) pattern="0[0-9]{9,10}" maxlength="11">
        @error('phone')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="field field-half">
        <label for="address" class="label">Địa chỉ</label>
        <input type="text" id="address" name="address" value="{{ old('address', $profile?->address) }}"
               @class(['input', 'is-invalid' => $errors->has('address')]) maxlength="255">
        @error('address')
            <div class="field-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Lưu nhân viên</button>
    <a href="{{ $employee->exists ? route('employees.show', $employee) : route('employees.index') }}" class="btn btn-secondary">Hủy</a>
</div>
