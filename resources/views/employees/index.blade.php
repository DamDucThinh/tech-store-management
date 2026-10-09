@extends('layouts.app')

@section('title', 'Nhân viên')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Nhân viên</h1>
            <p class="page-subtitle">Tài khoản đăng nhập và thông tin nhân viên bán hàng.</p>
        </div>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">+ Thêm nhân viên</a>
    </div>

    <form method="GET" class="filters">
        <input type="search" name="q" value="{{ request('q') }}" class="input filter-search" placeholder="Tìm theo họ tên hoặc username" aria-label="Tìm nhân viên">
        <select name="role" class="select" aria-label="Lọc theo vai trò">
            <option value="">Mọi vai trò</option>
            @foreach (\App\Enums\EmployeeRole::cases() as $role)
                <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
        <select name="status" class="select" aria-label="Lọc theo trạng thái">
            <option value="">Mọi trạng thái</option>
            @foreach (\App\Enums\EmployeeStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Lọc</button>
        @if (request()->hasAny(['q', 'role', 'status']))
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Bỏ lọc</a>
        @endif
    </form>

    <div class="card">
        <div class="table-wrap">
            <table class="table table-cards">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Vai trò</th>
                        <th>Số điện thoại</th>
                        <th>Status</th>
                        <th class="text-right">Số đơn</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td class="cell-primary">
                                <div class="cell-media">
                                    @include('partials.avatar', ['profile' => $employee->profile])
                                    <div>
                                        <a href="{{ route('employees.show', $employee) }}" class="strong">{{ $employee->displayName() }}</a>
                                        <div class="small muted">{{ $employee->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Vai trò">{{ $employee->role->label() }}</td>
                            <td data-label="Số điện thoại">{{ $employee->profile?->phone ?: '—' }}</td>
                            <td data-label="Status">@include('partials.badge', ['status' => $employee->status])</td>
                            <td data-label="Số đơn" class="text-right">{{ $employee->orders_count }}</td>
                            <td>
                                <div class="cell-actions">
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-secondary btn-sm">Sửa</a>
                                    @unless ($employee->is(auth()->user()))
                                        @include('partials.delete-form', [
                                            'action' => route('employees.destroy', $employee),
                                            'confirm' => "Xóa nhân viên \"{$employee->displayName()}\"?",
                                        ])
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Không có nhân viên nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $employees->links() }}
@endsection
