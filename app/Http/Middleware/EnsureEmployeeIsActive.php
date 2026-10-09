<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Nếu tài khoản bị khóa trong lúc đang đăng nhập thì đăng xuất ngay ở request tiếp theo.
 */
class EnsureEmployeeIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $employee = $request->user();

        if ($employee && ! $employee->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['username' => 'Tài khoản đã bị khóa. Vui lòng liên hệ quản lý.']);
        }

        return $next($request);
    }
}
