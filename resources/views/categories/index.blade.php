@extends('layouts.app')

@section('title', 'Danh mục')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Danh mục</h1>
            <p class="page-subtitle">Nhóm sản phẩm theo loại hàng.</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Thêm danh mục</a>
    </div>

    <form method="GET" class="filters">
        <input type="search" name="q" value="{{ request('q') }}" class="input filter-search" placeholder="Tìm theo tên danh mục" aria-label="Tìm theo tên danh mục">
        <button type="submit" class="btn btn-secondary">Tìm</button>
    </form>

    <div class="card">
        <div class="table-wrap">
            <table class="table table-cards">
                <thead>
                    <tr>
                        <th>Tên danh mục</th>
                        <th class="text-right">Số sản phẩm</th>
                        <th>Ngày tạo</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="cell-primary strong">{{ $category->name }}</td>
                            <td data-label="Số sản phẩm" class="text-right">
                                <a href="{{ route('products.index', ['category_id' => $category->id]) }}">{{ $category->products_count }}</a>
                            </td>
                            <td data-label="Ngày tạo">{{ $category->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="cell-actions">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-secondary btn-sm">Sửa</a>
                                    @include('partials.delete-form', [
                                        'action' => route('categories.destroy', $category),
                                        'confirm' => "Xóa danh mục \"{$category->name}\"?",
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">Không có danh mục nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $categories->links() }}
@endsection
