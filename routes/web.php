<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PracticeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Trang chủ tạm (Pha 0). Marketing/SEO sẽ dựng ở Pha 2 (Blade).
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'appName' => config('app.name'),
        'phase' => 'Pha 1 — Nền tảng',
    ]);
})->name('home');

// Khách (chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Đã đăng nhập — gác qua chuỗi middleware như v1 (bỏ phần lớp học/google).
Route::middleware(['auth', 'user.blocked', 'user.expired', 'session.limit', 'password.changed'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Practice — đọc set từ db1, làm bài, lưu attempt vào db2 (mốc chốt Pha 1).
    Route::get('/practice/{set}', [PracticeController::class, 'show'])->name('practice.show');
    Route::post('/practice/{set}/attempt', [PracticeController::class, 'store'])
        ->middleware('throttle:10,1')->name('practice.store');
});
