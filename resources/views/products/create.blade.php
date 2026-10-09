@extends('layouts.app')

@section('title', 'Thêm sản phẩm')

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('products.index') }}" class="back-link">&larr; Sản phẩm</a>
            <h1 class="page-title">Thêm sản phẩm</h1>
        </div>
    </div>

    <div class="card form-card">
        <div class="card-body">
            {{-- Submit gửi POST /products, route gọi ProductController@store.
                 enctype multipart/form-data để gửi được file ảnh. --}}
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf
                @include('products._form')
            </form>
        </div>
    </div>
@endsection
