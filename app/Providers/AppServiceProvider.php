<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Brand cố định = nhaiaptis cho MỌI nơi (title SEO, email…) — không lệ thuộc APP_NAME/.env.
        config(['app.name' => 'nhaiaptis']);
    }
}
