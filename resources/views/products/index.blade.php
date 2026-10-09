@extends('layouts.app')

@section('title', 'Sản phẩm')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Sản phẩm</h1>
            <p class="page-subtitle">{{ $products->total() }} sản phẩm</p>
        </div>
        @can('manager')
            <a href="{{ route('products.create') }}" class="btn btn-primary">+ Thêm sản phẩm</a>
        @endcan
    </div>

    <form method="GET" class="filters">
        <input type="search" name="q" value="{{ request('q') }}" class="input filter-search" placeholder="Tìm theo tên sản phẩm" aria-label="Tìm theo tên sản phẩm">
        <select name="category_id" class="select" aria-label="Lọc theo danh mục">
            <option value="">Tất cả danh mục</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="status" class="select" aria-label="Lọc theo trạng thái">
            <option value="">Mọi trạng thái</option>
            @foreach (\App\Enums\ProductStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Lọc</button>
        @if (request()->hasAny(['q', 'category_id', 'status']))
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Bỏ lọc</a>
        @endif
    </form>

    <div class="card">
        <div class="table-wrap">
            <table class="table table-cards">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Danh mục</th>
                        <th class="text-right">Giá bán</th>
                        <th>Trạng thái</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="cell-primary">
                                <div class="cell-media">
                                    @include('partials.product-thumb')
                                    <a href="{{ route('products.show', $product) }}" class="strong">{{ $product->name }}</a>
                                </div>
                            </td>
                            <td data-label="Danh mục">{{ $product->category->name }}</td>
                            <td data-label="Giá bán" class="text-right nowrap strong">@money($product->price)</td>
                            <td data-label="Trạng thái">@include('partials.badge', ['status' => $product->status])</td>
                            <td>
                                <div class="cell-actions">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-secondary btn-sm">Xem</a>
                                    @can('manager')
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm">Sửa</a>
                                        @include('partials.delete-form', [
                                            'action' => route('products.destroy', $product),
                                            'confirm' => "Xóa sản phẩm \"{$product->name}\"?",
                                        ])
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">Không tìm thấy sản phẩm nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $products->links() }}
@endsection
