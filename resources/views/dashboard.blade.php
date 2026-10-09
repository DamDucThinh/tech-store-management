@extends('layouts.app')

@section('title', 'Tổng quan')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Xin chào, {{ auth()->user()->displayName() }}</h1>
            <p class="page-subtitle">
                {{ auth()->user()->isManager() ? 'Số liệu toàn cửa hàng.' : 'Số liệu các đơn hàng do bạn tạo.' }}
                Doanh thu chỉ tính đơn đã hoàn thành.
            </p>
        </div>
        <div class="actions">
            @can('manager')
                <a href="{{ route('products.create') }}" class="btn btn-secondary">Thêm sản phẩm</a>
            @endcan
            <a href="{{ route('orders.create') }}" class="btn btn-primary">Tạo đơn hàng</a>
        </div>
    </div>

    <div class="stats">
        <div class="card stat">
            <div class="stat-label">Doanh thu tháng {{ now()->format('m/Y') }}</div>
            <div class="stat-value is-success">@money($stats['revenue_this_month'])</div>
        </div>
        <div class="card stat">
            <div class="stat-label">Tổng doanh thu</div>
            <div class="stat-value">@money($stats['revenue'])</div>
        </div>
        <div class="card stat">
            <div class="stat-label">Tổng số đơn hàng</div>
            <div class="stat-value">{{ $stats['orders'] }}</div>
        </div>
        <div class="card stat">
            <div class="stat-label">Sản phẩm đang bán</div>
            <div class="stat-value">{{ $stats['selling_products'] }}</div>
        </div>
    </div>

    <div class="status-strip">
        @foreach (\App\Enums\OrderStatus::cases() as $status)
            <a href="{{ route('orders.index', ['status' => $status->value]) }}" class="card status-chip">
                @include('partials.badge', ['status' => $status])
                <strong>{{ $statusCounts[$status->value] ?? 0 }}</strong>
            </a>
        @endforeach
    </div>

    <div class="columns">
        <div class="card col-main">
            <div class="card-header">
                Đơn hàng gần đây
                <a href="{{ route('orders.index') }}" class="small">Xem tất cả</a>
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
                                    <div class="small muted">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td data-label="Khách hàng">{{ $order->customer_name }}</td>
                                <td data-label="Trạng thái">@include('partials.badge', ['status' => $order->status])</td>
                                <td data-label="Tổng tiền" class="text-right strong">@money($order->total_amount)</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Chưa có đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card col-side">
            <div class="card-header">Sản phẩm bán chạy</div>
            <div class="table-wrap">
                <table class="table">
                    <tbody>
                        @forelse ($topProducts as $row)
                            <tr>
                                <td>
                                    <div class="cell-media">
                                        @include('partials.product-thumb', ['product' => $row->product])
                                        <div>
                                            <a href="{{ route('products.show', $row->product_id) }}" class="strong">{{ $row->product->name }}</a>
                                            <div class="small muted">Đã bán {{ $row->total_quantity }} · @money($row->total_revenue)</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="empty">Chưa có đơn hoàn thành.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
