<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Inertia: chia sẻ dữ liệu chung + xử lý version asset cho khu Vue.
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
            'user.blocked' => \App\Http\Middleware\CheckUserBlocked::class,
            'user.expired' => \App\Http\Middleware\CheckAccountExpiration::class,
            'password.changed' => \App\Http\Middleware\MustChangePassword::class,
            'session.limit' => \App\Http\Middleware\SessionLimit::class,
        ]);

        // Webhook PayOS gọi từ ngoài, không có CSRF token.
        $middleware->validateCsrfTokens(except: [
            'webhooks/payos',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
