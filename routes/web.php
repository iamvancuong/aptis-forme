<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

// ── Marketing / SEO (Blade, server-render) ──────────────────────────────
Route::get('/', function () {
    return view('welcome', [
        'packages' => config('pricing.packages'),
        'catalogStats' => \App\Http\Controllers\CatalogController::summary(),
    ]);
})->name('home');

// Ngân hàng đề (công khai — danh mục toàn bộ đề)
Route::get('/ngan-hang-de', [\App\Http\Controllers\CatalogController::class, 'index'])->name('catalog');

// Học thử (không cần đăng nhập, mỗi kỹ năng 1 lần)
Route::get('/hoc-thu/{skill}', [\App\Http\Controllers\TrialController::class, 'show'])->name('trial.show');
Route::post('/hoc-thu/{set}/check', [\App\Http\Controllers\TrialController::class, 'check'])
    ->middleware('throttle:120,1')->name('trial.check');

// Nhập mã khuyến mãi → học free 1 ngày
Route::get('/nhap-ma', [\App\Http\Controllers\PromoController::class, 'show'])->name('promo.show');
Route::post('/nhap-ma', [\App\Http\Controllers\PromoController::class, 'redeem'])
    ->middleware('throttle:10,1')->name('promo.redeem');

Route::view('/chinh-sach-hoan-tien', 'policy.refund')->name('policy.refund');
Route::view('/gioi-thieu', 'pages.gioi-thieu')->name('about');
Route::view('/luyen-thi-aptis', 'pages.luyen-thi-aptis')->name('aptis');

// Sitemap XML — chỉ trang công khai.
Route::get('/sitemap.xml', function () {
    $now = now()->toAtomString();
    $urls = [
        ['loc' => route('home'), 'priority' => '1.0', 'freq' => 'weekly'],
        ['loc' => route('aptis'), 'priority' => '0.9', 'freq' => 'monthly'],
        ['loc' => route('catalog'), 'priority' => '0.8', 'freq' => 'weekly'],
        ['loc' => route('promo.show'), 'priority' => '0.7', 'freq' => 'weekly'],
        ['loc' => route('about'), 'priority' => '0.8', 'freq' => 'monthly'],
        ['loc' => route('register'), 'priority' => '0.9', 'freq' => 'weekly'],
        ['loc' => route('policy.refund'), 'priority' => '0.3', 'freq' => 'yearly'],
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $u) {
        $xml .= "  <url><loc>{$u['loc']}</loc><lastmod>{$now}</lastmod>"
              . "<changefreq>{$u['freq']}</changefreq><priority>{$u['priority']}</priority></url>\n";
    }
    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

// robots.txt động.
Route::get('/robots.txt', function () {
    $lines = [
        'User-agent: *',
        'Allow: /',
        'Disallow: /admin',
        'Disallow: /dashboard',
        'Disallow: /thanh-toan',
        'Disallow: /doi-mat-khau',
        '',
        'Sitemap: ' . route('sitemap'),
    ];

    return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain']);
});

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

    // Kỹ năng → part → bộ đề
    Route::get('/skills/{skill}', [\App\Http\Controllers\SkillController::class, 'show'])->name('skills.show');

    Route::get('/practice/{set}', [PracticeController::class, 'show'])->name('practice.show');
    Route::post('/practice/{set}/attempt', [PracticeController::class, 'store'])
        ->middleware('throttle:10,1')->name('practice.store');
    Route::post('/practice/{set}/check', [PracticeController::class, 'check'])
        ->middleware('throttle:120,1')->name('practice.check');

    // Thi thử (Mock Test) — reading/listening (writing/speaking chờ Pha 4 AI)
    Route::get('/mock-test/{skill}', [\App\Http\Controllers\MockTestController::class, 'create'])->name('mock-test.create');
    Route::post('/mock-test', [\App\Http\Controllers\MockTestController::class, 'start'])->name('mock-test.start');
    Route::get('/mock-test/{mockTest}/exam', [\App\Http\Controllers\MockTestController::class, 'show'])->name('mock-test.show');
    Route::post('/mock-test/{mockTest}/submit', [\App\Http\Controllers\MockTestController::class, 'submit'])->name('mock-test.submit');
    Route::get('/mock-test/{mockTest}/result', [\App\Http\Controllers\MockTestController::class, 'result'])->name('mock-test.result');

    // Bảng xếp hạng
    Route::get('/leaderboard', [\App\Http\Controllers\LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Chấm AI Writing / Speaking
    Route::post('/ai/grade-writing/{answer}', [\App\Http\Controllers\AiController::class, 'gradeWriting'])
        ->middleware('throttle:20,1')->name('ai.grade-writing');
    Route::post('/ai/grade-speaking/{answer}', [\App\Http\Controllers\AiController::class, 'gradeSpeaking'])
        ->middleware('throttle:20,1')->name('ai.grade-speaking');

    // Lịch sử làm bài
    Route::get('/history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('history.index');
    Route::get('/history/{attempt}', [\App\Http\Controllers\HistoryController::class, 'show'])->name('history.show');

    // Hướng dẫn (đọc từ db1)
    Route::get('/instructions', [\App\Http\Controllers\InstructionController::class, 'index'])->name('instructions.index');
    Route::get('/instructions/{slug}', [\App\Http\Controllers\InstructionController::class, 'show'])->name('instructions.show');

    // Đổi mật khẩu (buộc đổi lần đầu với tài khoản mua)
    Route::get('/doi-mat-khau', [\App\Http\Controllers\PasswordChangeController::class, 'edit'])->name('password.change');
    Route::post('/doi-mat-khau', [\App\Http\Controllers\PasswordChangeController::class, 'update'])->name('password.update');

    // Audio câu hỏi — signed trên nền auth (link copy vô dụng với người ngoài).
    Route::get('/media/questions/{question}/audio/{index?}', [\App\Http\Controllers\MediaController::class, 'questionAudio'])
        ->middleware('signed')->whereNumber('index')->name('media.question-audio');
});

// ── Admin (chỉ Học viên + Thanh toán) ───────────────────────────────────
Route::middleware(['auth', 'user.blocked', 'admin'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/block', [\App\Http\Controllers\Admin\UserController::class, 'block'])->name('users.block');
        Route::post('/users/{user}/unblock', [\App\Http\Controllers\Admin\UserController::class, 'unblock'])->name('users.unblock');
        Route::post('/users/{user}/extend', [\App\Http\Controllers\Admin\UserController::class, 'extend'])->name('users.extend');
        Route::post('/users/{user}/add-ai', [\App\Http\Controllers\Admin\UserController::class, 'addAi'])->name('users.add-ai');
        Route::post('/users/{user}/reset-ai', [\App\Http\Controllers\Admin\UserController::class, 'resetAi'])->name('users.reset-ai');

        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');

        // Mã khuyến mãi
        Route::get('/promo-codes', [\App\Http\Controllers\Admin\PromoCodeController::class, 'index'])->name('promo-codes.index');
        Route::post('/promo-codes', [\App\Http\Controllers\Admin\PromoCodeController::class, 'store'])->name('promo-codes.store');
        Route::put('/promo-codes/{promoCode}', [\App\Http\Controllers\Admin\PromoCodeController::class, 'update'])->name('promo-codes.update');
    });
