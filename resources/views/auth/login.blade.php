<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <main class="auth">
        <div class="card auth-card">
            <div class="auth-brand">
                <span class="brand-mark">MS</span>
                <h1>{{ config('app.name') }}</h1>
                <span class="muted small">Đăng nhập để tiếp tục</span>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field">
                    <label for="username" class="label">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                           @class(['input', 'is-invalid' => $errors->has('username')])
                           autocomplete="username" required autofocus>
                    @error('username')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="password" class="label">Mật khẩu</label>
                    <input type="password" id="password" name="password"
                           @class(['input', 'is-invalid' => $errors->has('password')])
                           autocomplete="current-password" required>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
            </form>

            @env('local')
                <div class="demo-accounts">
                    Tài khoản demo, mật khẩu đều là <code>password</code>:
                    <ul>
                        <li><code>manager</code> Quản lý</li>
                        <li><code>staff</code> Nhân viên bán hàng</li>
                        <li><code>tuan.pham</code> tài khoản đã bị khóa</li>
                    </ul>
                </div>
            @endenv
        </div>
    </main>
</body>
</html>
