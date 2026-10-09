@extends('layouts.app')

@section('title', "Đơn hàng #{$order->id}")

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('orders.index') }}" class="back-link no-print">&larr; Đơn hàng</a>
            <h1 class="page-title">Đơn hàng #{{ $order->id }}</h1>
            <p class="page-subtitle">Tạo lúc {{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="actions no-print">
            <button type="button" class="btn btn-secondary" onclick="window.print()">In đơn</button>
            @can('delete', $order)
                @include('partials.delete-form', [
                    'action' => route('orders.destroy', $order),
                    'confirm' => "Xóa đơn hàng #{$order->id}? Các dòng hàng của đơn cũng bị xóa.",
                    'label' => 'Xóa đơn',
                    'small' => false,
                ])
            @endcan
        </div>
    </div>

    <div class="columns">
        <div class="col-main">
            <div class="card">
                <div class="card-header">Danh sách sản phẩm</div>
                <div class="table-wrap">
                    <table class="table table-cards">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-right">Đơn giá</th>
                                <th class="text-right">Số lượng</th>
                                <th class="text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="cell-primary">
                                        <div class="cell-media">
                                            @include('partials.product-thumb', ['product' => $item->product])
                                            <div>
                                                <a href="{{ route('products.show', $item->product) }}" class="strong">{{ $item->product->name }}</a>
                                                <div class="small muted">{{ $item->product->category->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Đơn giá" class="text-right nowrap">@money($item->price)</td>
                                    <td data-label="Số lượng" class="text-right">{{ $item->quantity }}</td>
                                    <td data-label="Thành tiền" class="text-right nowrap strong">@money($item->subtotal())</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="order-total">
                    Tổng tiền <strong>@money($order->total_amount)</strong>
                </div>
            </div>
        </div>

        <div class="col-side stack">
            <div class="card">
                <div class="card-header">Thông tin đơn hàng</div>
                <div class="card-body">
                    <dl class="detail-list">
                        <div class="detail-row">
                            <dt>Trạng thái</dt>
                            <dd>@include('partials.badge', ['status' => $order->status])</dd>
                        </div>
                        <div class="detail-row">
                            <dt>Khách hàng</dt>
                            <dd class="strong">{{ $order->customer_name }}</dd>
                        </div>
                        <div class="detail-row">
                            <dt>Số điện thoại</dt>
                            <dd>{{ $order->customer_phone }}</dd>
                        </div>
                        <div class="detail-row">
                            <dt>Nhân viên</dt>
                            <dd>{{ $order->employee->displayName() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="card no-print">
                <div class="card-header">Cập nhật trạng thái</div>
                <div class="card-body">
                    @can('updateStatus', $order)
                        <form method="POST" action="{{ route('orders.update-status', $order) }}" class="status-form">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="select" aria-label="Trạng thái mới" required>
                                @foreach ($order->status->nextStatuses() as $next)
                                    <option value="{{ $next->value }}">{{ $next->label() }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </form>
                        @error('status')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    @else
                        <p class="muted small" style="margin: 0">
                            Đơn đã ở trạng thái "{{ $order->status->label() }}", không thể thay đổi nữa.
                        </p>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
