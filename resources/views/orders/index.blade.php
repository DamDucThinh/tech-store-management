@extends('layouts.app')

@section('title', 'Đơn hàng')

@section('content')
    @php($isManager = auth()->user()->isManager())

    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $isManager ? 'Đơn hàng' : 'Đơn hàng của tôi' }}</h1>
            <p class="page-subtitle">
                {{ $isManager ? 'Tất cả đơn hàng của các nhân viên.' : 'Các đơn hàng do bạn tạo.' }}
            </p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">+ Tạo đơn hàng</a>
    </div>

    <form method="GET" class="filters">
        <input type="search" name="q" value="{{ request('q') }}" class="input filter-search" placeholder="Tên hoặc SĐT khách hàng" aria-label="Tìm khách hàng">
        <select name="status" class="select" aria-label="Lọc theo trạng thái">
            <option value="">Mọi trạng thái</option>
            @foreach (\App\Enums\OrderStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        @if ($isManager)
            <select name="employee_id" class="select" aria-label="Lọc theo nhân viên">
                <option value="">Tất cả nhân viên</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected((string) request('employee_id') === (string) $employee->id)>{{ $employee->displayName() }}</option>
                @endforeach
            </select>
        @endif
        <button type="submit" class="btn btn-secondary">Lọc</button>
        @if (request()->hasAny(['q', 'status', 'employee_id']))
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Bỏ lọc</a>
        @endif
    </form>

    <div class="card">
        <div class="table-wrap">
            <table class="table table-cards">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        @if ($isManager)<th>Nhân viên</th>@endif
                        <th>Trạng thái</th>
                        <th class="text-right">Tổng tiền</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="cell-primary">
                                <a href="{{ route('orders.show', $order) }}" class="strong">#{{ $order->id }}</a>
                                <div class="small muted">{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->items_count }} sản phẩm</div>
                            </td>
                            <td data-label="Khách hàng">
                                <div>{{ $order->customer_name }}</div>
                                <div class="small muted">{{ $order->customer_phone }}</div>
                            </td>
                            @if ($isManager)
                                <td data-label="Nhân viên">{{ $order->employee->displayName() }}</td>
                            @endif
                            <td data-label="Trạng thái">@include('partials.badge', ['status' => $order->status])</td>
                            <td data-label="Tổng tiền" class="text-right nowrap strong">@money($order->total_amount)</td>
                            <td>
                                <div class="cell-actions">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary btn-sm">Xem</a>
                                    @can('delete', $order)
                                        @include('partials.delete-form', [
                                            'action' => route('orders.destroy', $order),
                                            'confirm' => "Xóa đơn hàng #{$order->id}? Các dòng hàng của đơn cũng bị xóa.",
                                        ])
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="{{ $isManager ? 6 : 5 }}" class="empty">Không có đơn hàng nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $orders->links() }}
@endsection
