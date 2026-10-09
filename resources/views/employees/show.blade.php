@extends('layouts.app')

@section('title', $employee->displayName())

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('employees.index') }}" class="back-link">&larr; Nhân viên</a>
            <h1 class="page-title">{{ $employee->displayName() }}</h1>
        </div>
        <div class="actions">
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-secondary">Sửa</a>
            @unless ($employee->is(auth()->user()))
                @include('partials.delete-form', [
                    'action' => route('employees.destroy', $employee),
                    'confirm' => "Xóa nhân viên \"{$employee->displayName()}\"?",
                    'small' => false,
                ])
            @endunless
        </div>
    </div>

    <div class="columns">
        <div class="card col-side">
            <div class="profile-head">
                @include('partials.avatar', ['profile' => $employee->profile, 'size' => 'lg'])
                <div>
                    <div class="strong">{{ $employee->displayName() }}</div>
                    <div class="small muted">{{ $employee->role->label() }}</div>
                </div>
                @include('partials.badge', ['status' => $employee->status])
            </div>
            <div class="card-body">
                <dl class="detail-list">
                    <div class="detail-row"><dt>Username</dt><dd><code>{{ $employee->username }}</code></dd></div>
                    <div class="detail-row"><dt>Email</dt><dd>{{ $employee->profile?->email ?: '—' }}</dd></div>
                    <div class="detail-row"><dt>Số điện thoại</dt><dd>{{ $employee->profile?->phone ?: '—' }}</dd></div>
                    <div class="detail-row"><dt>Địa chỉ</dt><dd>{{ $employee->profile?->address ?: '—' }}</dd></div>
                    <div class="detail-row"><dt>Số đơn đã tạo</dt><dd>{{ $employee->orders_count }}</dd></div>
                    <div class="detail-row"><dt>Doanh số</dt><dd class="strong">@money($revenue)</dd></div>
                </dl>
            </div>
        </div>

        <div class="card col-main">
            <div class="card-header">
                Đơn hàng gần đây
                <a href="{{ route('orders.index', ['employee_id' => $employee->id]) }}" class="small">Xem tất cả</a>
            </div>
            <div class="table-wrap">
                <table class="table table-cards">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Trạng thái</th>
                            <th class="text-right">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr>
                                <td class="cell-primary">
                                    <a href="{{ route('orders.show', $order) }}" class="strong">#{{ $order->id }}</a>
                                    <div class="small muted">{{ $order->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td data-label="Khách hàng">{{ $order->customer_name }}</td>
                                <td data-label="Trạng thái">@include('partials.badge', ['status' => $order->status])</td>
                                <td data-label="Tổng tiền" class="text-right nowrap">@money($order->total_amount)</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Nhân viên chưa tạo đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
