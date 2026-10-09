<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Đăng nhập: chỉ dành cho người chưa đăng nhập. Giới hạn 5 lần thử mỗi phút.
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

// auth: phải đăng nhập. active: tài khoản không bị khóa.
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    // Chỉ Quản lý. Đặt trước route products/{product} để "products/create"
    // không bị hiểu nhầm là xem sản phẩm có id = "create".
    Route::middleware('can:manager')->group(function () {
        Route::resource('categories', CategoryController::class)->except('show');
        // POST /products -> ProductController@store (name: products.store)
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('employees', EmployeeController::class);
    });

    // Mọi nhân viên: xem sản phẩm, tạo và quản lý đơn hàng.
    // Quyền trên từng đơn hàng được kiểm tra trong OrderController bằng OrderPolicy.
    Route::resource('products', ProductController::class)->only(['index', 'show']);
    Route::resource('orders', OrderController::class)->except(['edit', 'update']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
});
