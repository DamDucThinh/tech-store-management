@extends('layouts.app')

@section('title', 'Thêm danh mục')

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('categories.index') }}" class="back-link">&larr; Danh mục</a>
            <h1 class="page-title">Thêm danh mục</h1>
        </div>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                @include('categories._form')
            </form>
        </div>
    </div>
@endsection
