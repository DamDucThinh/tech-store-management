@extends('layouts.app')

@section('title', 'Sửa danh mục')

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('categories.index') }}" class="back-link">&larr; Danh mục</a>
            <h1 class="page-title">Sửa danh mục</h1>
        </div>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('categories.update', $category) }}">
                @csrf
                @method('PUT')
                @include('categories._form')
            </form>
        </div>
    </div>
@endsection
