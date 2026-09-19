<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountExpiration
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isExpired()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Tài khoản đã hết hạn. Vui lòng liên hệ admin để gia hạn.');
        }

        return $next($request);
    }
}
