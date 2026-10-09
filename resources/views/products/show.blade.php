@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('products.index') }}" class="back-link">&larr; Sản phẩm</a>
            <h1 class="page-title">{{ $product->name }}</h1>
        </div>
        @can('manager')
            <div class="actions">
                <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary">Sửa</a>
                @include('partials.delete-form', [
                    'action' => route('products.destroy', $product),
                    'confirm' => "Xóa sản phẩm \"{$product->name}\"?",
                    'small' => false,
                ])
            </div>
        @endcan
    </div>

    <div class="columns">
        <div class="card col-side">
            <div class="card-body">
                @if ($product->imageUrl())
                    <img src="{{ $product->imageUrl() }}" alt="{{ $product->name }}" class="product-image">
                @else
                    <div class="product-image product-image-empty">Chưa có ảnh</div>
                @endif
            </div>
        </div>

        <div class="card col-main">
            <div class="card-body">
                <div class="price-tag">@money($product->price)</div>
                <dl class="detail-list">
                    <div class="detail-row">
                        <dt>Danh mục</dt>
                        <dd><a href="{{ route('products.index', ['category_id' => $product->category_id]) }}">{{ $product->category->name }}</a></dd>
                    </div>
                    <div class="detail-row">
                        <dt>Trạng thái</dt>
                        <dd>@include('partials.badge', ['status' => $product->status])</dd>
                    </div>
                    <div class="detail-row">
                        <dt>Đã bán</dt>
                        <dd>{{ $soldQuantity }} (đơn hoàn thành)</dd>
                    </div>
                    <div class="detail-row">
                        <dt>Mô tả</dt>
                        <dd><p class="description">{{ $product->description ?: '—' }}</p></dd>
                    </div>
                    <div class="detail-row">
                        <dt>Cập nhật</dt>
                        <dd>{{ $product->updated_at->format('d/m/Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection
