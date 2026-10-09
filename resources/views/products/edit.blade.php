@extends('layouts.app')

@section('title', 'Sửa sản phẩm')

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('products.show', $product) }}" class="back-link">&larr; {{ $product->name }}</a>
            <h1 class="page-title">Sửa sản phẩm</h1>
        </div>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('products._form')
            </form>
        </div>
    </div>
@endsection
