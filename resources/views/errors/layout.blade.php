<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <main class="error-page">
        <div class="card error-box">
            <div class="error-code">@yield('code')</div>
            <p>@yield('message')</p>
            <a href="{{ url('/') }}" class="btn btn-primary">Về trang tổng quan</a>
        </div>
    </main>
</body>
</html>
