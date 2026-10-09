<?php

namespace App\Providers;

use App\Models\Employee;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Quyền "manager": quản lý danh mục, sản phẩm, nhân viên và mọi đơn hàng.
        Gate::define('manager', fn (Employee $employee) => $employee->isManager());

        Paginator::defaultView('partials.pagination');

        // @money($amount) hiển thị tiền theo định dạng Việt Nam: 1.250.000 ₫
        Blade::directive('money', fn (string $expression) => "<?php echo number_format((float) ($expression), 0, ',', '.').' ₫'; ?>");
    }
}
