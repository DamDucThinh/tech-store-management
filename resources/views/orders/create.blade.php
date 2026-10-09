@extends('layouts.app')

@section('title', 'Tạo đơn hàng')

@php
    $oldItems = old('items');
    $rows = is_array($oldItems) && $oldItems !== [] ? $oldItems : [['product_id' => '', 'quantity' => 1]];
    $nextIndex = max(array_map('intval', array_keys($rows))) + 1;
@endphp

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('orders.index') }}" class="back-link">&larr; Đơn hàng</a>
            <h1 class="page-title">Tạo đơn hàng</h1>
            <p class="page-subtitle">Nhân viên bán hàng: {{ auth()->user()->displayName() }}. Đơn mới có trạng thái Chờ xử lý.</p>
        </div>
    </div>

    @if ($products->isEmpty())
        <div class="alert alert-error">
            <span>Chưa có sản phẩm nào đang bán nên chưa thể tạo đơn hàng.</span>
        </div>
    @else
        <form method="POST" action="{{ route('orders.store') }}">
            @csrf

            <div class="columns">
                <div class="card col-side">
                    <div class="card-header">Khách hàng</div>
                    <div class="card-body">
                        <div class="field">
                            <label for="customer_name" class="label">Tên khách hàng <span class="req">*</span></label>
                            <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                   @class(['input', 'is-invalid' => $errors->has('customer_name')]) required maxlength="100">
                            @error('customer_name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="customer_phone" class="label">Số điện thoại <span class="req">*</span></label>
                            <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}"
                                   @class(['input', 'is-invalid' => $errors->has('customer_phone')])
                                   required pattern="0[0-9]{9,10}" maxlength="11" placeholder="Ví dụ: 0912345678">
                            @error('customer_phone')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card col-main">
                    <div class="card-header">Danh sách sản phẩm</div>

                    @error('items')
                        <div class="card-body"><div class="alert alert-error"><span>{{ $message }}</span></div></div>
                    @enderror

                    <div class="table-wrap">
                        <table class="table table-cards">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th class="text-right">Thành tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="order-lines" data-next-index="{{ $nextIndex }}">
                                @foreach ($rows as $index => $item)
                                    @include('orders._line', ['index' => $index, 'item' => $item])
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="order-lines-footer">
                        <button type="button" id="add-line" class="btn btn-secondary btn-sm">+ Thêm sản phẩm</button>
                        <span class="small muted">Đơn giá lấy theo giá bán hiện tại và được lưu cố định vào đơn.</span>
                    </div>

                    <div class="order-total">
                        Tổng tiền <strong id="order-total">0 ₫</strong>
                    </div>
                </div>
            </div>

            <div class="form-actions" style="margin-top: 20px">
                <button type="submit" class="btn btn-primary">Lưu đơn hàng</button>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>

        <template id="order-line-template">
            @include('orders._line', ['index' => '__INDEX__', 'item' => ['product_id' => '', 'quantity' => 1]])
        </template>
    @endif
@endsection
