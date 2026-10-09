@extends('layouts.app')

@section('title', 'Thêm nhân viên')

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('employees.index') }}" class="back-link">&larr; Nhân viên</a>
            <h1 class="page-title">Thêm nhân viên</h1>
        </div>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                @csrf
                @include('employees._form')
            </form>
        </div>
    </div>
@endsection
