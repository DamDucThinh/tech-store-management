<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    @php($me = auth()->user())

    <div class="app">
        <aside class="sidebar" id="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <span class="brand-mark">TS</span>
                Tech Store
            </a>

            <nav class="nav" aria-label="Menu chính">
                <a @class(['nav-link', 'active' => request()->routeIs('dashboard')]) href="{{ route('dashboard') }}">
                    @include('partials.icon', ['name' => 'home']) Tổng quan
                </a>

                <div class="nav-label">Bán hàng</div>
                <a @class(['nav-link', 'active' => request()->routeIs('orders.*')]) href="{{ route('orders.index') }}">
                    @include('partials.icon', ['name' => 'orders']) {{ $me->isManager() ? 'Đơn hàng' : 'Đơn hàng của tôi' }}
                </a>
                <a @class(['nav-link', 'active' => request()->routeIs('products.*')]) href="{{ route('products.index') }}">
                    @include('partials.icon', ['name' => 'products']) Sản phẩm
                </a>

                @can('manager')
                    <div class="nav-label">Quản lý</div>
                    <a @class(['nav-link', 'active' => request()->routeIs('categories.*')]) href="{{ route('categories.index') }}">
                        @include('partials.icon', ['name' => 'categories']) Danh mục
                    </a>
                    <a @class(['nav-link', 'active' => request()->routeIs('employees.*')]) href="{{ route('employees.index') }}">
                        @include('partials.icon', ['name' => 'employees']) Nhân viên
                    </a>
                @endcan
            </nav>

            <div class="sidebar-user">
                @include('partials.avatar', ['profile' => $me->profile, 'size' => 'sm'])
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ $me->displayName() }}</div>
                    <div class="sidebar-user-role">{{ $me->role->label() }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                    @csrf
                    <button type="submit" class="icon-button" title="Đăng xuất" aria-label="Đăng xuất">
                        @include('partials.icon', ['name' => 'logout'])
                    </button>
                </form>
            </div>
        </aside>
        <div class="sidebar-backdrop" data-sidebar-toggle></div>

        <div class="main">
            <header class="topbar">
                <button type="button" class="icon-button" data-sidebar-toggle aria-controls="sidebar" aria-label="Mở menu">
                    @include('partials.icon', ['name' => 'menu'])
                </button>
                Tech Store
            </header>

            <main class="content">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
