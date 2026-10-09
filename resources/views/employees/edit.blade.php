@extends('layouts.app')

@section('title', 'Sửa nhân viên')

@section('content')
    <div class="page-header">
        <div>
            <a href="{{ route('employees.show', $employee) }}" class="back-link">&larr; {{ $employee->displayName() }}</a>
            <h1 class="page-title">Sửa nhân viên</h1>
        </div>
    </div>

    <div class="card form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('employees._form')
            </form>
        </div>
    </div>
@endsection
