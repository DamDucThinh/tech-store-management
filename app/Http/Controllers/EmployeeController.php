<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    private const ACCOUNT_FIELDS = ['username', 'role', 'status'];

    private const PROFILE_FIELDS = ['name', 'email', 'phone', 'address'];

    public function index(Request $request): View
    {
        $employees = Employee::query()
            ->with('profile')
            ->withCount('orders')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = '%'.$request->string('q').'%';
                $query->where(fn ($q) => $q->where('username', 'like', $keyword)
                    ->orWhereHas('profile', fn ($p) => $p->where('name', 'like', $keyword)));
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderBy('username')
            ->paginate(10)
            ->withQueryString();

        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        return view('employees.create', ['employee' => new Employee]);
    }

    /**
     * Tạo tài khoản (employees) và profile (employee_profiles, quan hệ 1-1)
     * trong cùng một transaction.
     */
    public function store(EmployeeRequest $request): RedirectResponse
    {
        $profile = $request->safe()->only(self::PROFILE_FIELDS);

        if ($request->hasFile('avatar')) {
            $profile['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $employee = DB::transaction(function () use ($request, $profile) {
            $employee = Employee::create($request->safe()->only([...self::ACCOUNT_FIELDS, 'password']));
            $employee->profile()->create($profile);

            return $employee;
        });

        return redirect()->route('employees.index')
            ->with('success', "Đã thêm nhân viên \"{$employee->displayName()}\".");
    }

    public function show(Employee $employee): View
    {
        $employee->load('profile')->loadCount('orders');

        $revenue = $employee->orders()->where('status', OrderStatus::Completed)->sum('total_amount');
        $recentOrders = $employee->orders()->latest()->latest('id')->take(10)->get();

        return view('employees.show', compact('employee', 'revenue', 'recentOrders'));
    }

    public function edit(Employee $employee): View
    {
        $employee->load('profile');

        return view('employees.edit', compact('employee'));
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $account = $request->safe()->only(self::ACCOUNT_FIELDS);
        $profile = $request->safe()->only(self::PROFILE_FIELDS);

        // Không cho tự đổi vai trò hoặc tự khóa chính mình, tránh hệ thống mất người quản lý.
        if ($employee->is($request->user())) {
            $account['role'] = $employee->role;
            $account['status'] = $employee->status;
        }

        if ($request->filled('password')) {
            $account['password'] = $request->validated('password');
        }

        $oldAvatar = $employee->profile?->avatar;

        if ($request->hasFile('avatar') || $request->boolean('remove_avatar')) {
            $profile['avatar'] = $request->hasFile('avatar')
                ? $request->file('avatar')->store('avatars', 'public')
                : null;
        }

        DB::transaction(function () use ($employee, $account, $profile) {
            $employee->update($account);
            $employee->profile()->updateOrCreate([], $profile);
        });

        if (array_key_exists('avatar', $profile) && $oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return redirect()->route('employees.show', $employee)
            ->with('success', "Đã cập nhật nhân viên \"{$employee->fresh('profile')->displayName()}\".");
    }

    public function destroy(Request $request, Employee $employee): RedirectResponse
    {
        if ($employee->is($request->user())) {
            return back()->with('error', 'Không thể tự xóa tài khoản đang đăng nhập.');
        }

        $orderCount = $employee->orders()->count();

        if ($orderCount > 0) {
            return back()->with('error', "Không thể xóa \"{$employee->displayName()}\" vì nhân viên đã tạo {$orderCount} đơn hàng. Hãy chuyển trạng thái tài khoản sang \"Đã khóa\" thay vì xóa.");
        }

        $name = $employee->displayName();
        $avatar = $employee->profile?->avatar;

        $employee->delete(); // profile bị xóa theo nhờ cascadeOnDelete

        if ($avatar) {
            Storage::disk('public')->delete($avatar);
        }

        return redirect()->route('employees.index')
            ->with('success', "Đã xóa nhân viên \"{$name}\".");
    }
}
