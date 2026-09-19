<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\RegistrationController;
use App\Models\Content\Feedback;
use App\Models\Content\HighScore;
use Illuminate\Support\Facades\Route;

// ── Marketing / SEO (Blade, server-render) ──────────────────────────────
Route::get('/', function () {
    return view('welcome', [
        'packages' => config('pricing.packages'),
        'feedbacks' => Feedback::where('is_active', true)->latest()->take(3)->get(),
        'highScores' => HighScore::where('is_active', true)->latest()->take(6)->get(),
    ]);
})->name('home');

Route::view('/chinh-sach-hoan-tien', 'policy.refund')->name('policy.refund');

// ── Đăng ký + Thanh toán (public) ───────────────────────────────────────
// Link giới thiệu sale: /dk/M1/thang
Route::get('/dk/{sale}/{goi?}', [RegistrationController::class, 'referral'])->name('referral');

Route::get('/thanh-toan/{order}', [PaymentController::class, 'show'])
    ->middleware('signed')->name('payment.show');
Route::get('/thanh-toan/{order}/thanh-cong', [PaymentController::class, 'return'])
    ->middleware('signed:code,id,cancel,status,orderCode')->name('payment.return');
Route::get('/thanh-toan/{order}/huy', [PaymentController::class, 'cancel'])
    ->middleware('signed:code,id,cancel,status,orderCode')->name('payment.cancel');
Route::post('/webhooks/payos', [PaymentController::class, 'webhook'])->name('payment.webhook');
// 🧪 Giả lập thanh toán — controller tự chặn khi PAYOS_FAKE khác true.
Route::get('/thanh-toan/{order}/gia-lap', [PaymentController::class, 'devFulfill'])->name('payment.dev-fulfill');

// ── Khách (chưa đăng nhập) ──────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');
});

// ── Đã đăng nhập ────────────────────────────────────────────────────────
Route::middleware(['auth', 'user.blocked', 'user.expired', 'session.limit', 'password.changed'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/practice/{set}', [PracticeController::class, 'show'])->name('practice.show');
    Route::post('/practice/{set}/attempt', [PracticeController::class, 'store'])
        ->middleware('throttle:10,1')->name('practice.store');

    // Lịch sử làm bài
    Route::get('/history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{attempt}', [\App\Http\Controllers\HistoryController::class, 'show'])->name('history.show');

    // Audio câu hỏi — signed trên nền auth (link copy vô dụng với người ngoài).
    Route::get('/media/questions/{question}/audio/{index?}', [\App\Http\Controllers\MediaController::class, 'questionAudio'])
        ->middleware('signed')->whereNumber('index')->name('media.question-audio');
});

// ── Admin (chỉ Học viên + Thanh toán) ───────────────────────────────────
Route::middleware(['auth', 'user.blocked', 'admin'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/block', [\App\Http\Controllers\Admin\UserController::class, 'block'])->name('users.block');
        Route::post('/users/{user}/unblock', [\App\Http\Controllers\Admin\UserController::class, 'unblock'])->name('users.unblock');
        Route::post('/users/{user}/extend', [\App\Http\Controllers\Admin\UserController::class, 'extend'])->name('users.extend');

        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    });
