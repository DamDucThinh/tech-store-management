<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Flow đăng nhập: validate -> tìm employee -> Hash::check password
     * -> kiểm tra tài khoản còn hoạt động -> tạo session -> redirect vào hệ thống.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ]);

        $employee = Employee::where('username', $credentials['username'])->first();

        // Sai username hay sai password đều báo chung một câu,
        // để người ngoài không dò được username nào có tồn tại.
        if (! $employee || ! Hash::check($credentials['password'], $employee->password)) {
            return back()
                ->withErrors(['username' => 'Tên đăng nhập hoặc mật khẩu không đúng.'])
                ->onlyInput('username');
        }

        if (! $employee->isActive()) {
            return back()
                ->withErrors(['username' => 'Tài khoản đã bị khóa. Vui lòng liên hệ quản lý.'])
                ->onlyInput('username');
        }

        Auth::login($employee);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
