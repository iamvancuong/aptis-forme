<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', 'Luyện thi APTIS online — luyện tập, thi thử và chấm AII.')">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-full font-sans antialiased bg-slate-50 text-slate-900">
    <header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/80 backdrop-blur">
        <div class="mx-auto max-w-6xl px-6 py-3.5 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-brand-600 to-violet-600 text-sm font-bold text-white">A</span>
                <span class="text-lg font-bold text-slate-900">{{ config('app.name') }}</span>
            </a>
            <nav class="flex items-center gap-1 text-sm sm:gap-2">
                <a href="{{ route('aptis') }}" class="hidden rounded-lg px-3 py-2 text-slate-600 hover:text-brand-600 sm:block">Luyện thi APTIS</a>
                <a href="{{ route('register') }}" class="rounded-lg px-3 py-2 text-slate-600 hover:text-brand-600">Đăng ký</a>
                <a href="{{ route('login') }}" class="rounded-lg bg-brand-600 px-4 py-2 font-medium text-white shadow-sm shadow-brand-600/20 hover:bg-brand-700">Đăng nhập</a>
            </nav>
        </div>
    </header>

    @if (session('error'))
        <div class="mx-auto max-w-5xl px-6 pt-4">
            <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">{{ session('error') }}</div>
        </div>
    @endif
    @if (session('success'))
        <div class="mx-auto max-w-5xl px-6 pt-4">
            <div class="rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200">{{ session('success') }}</div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-6 py-8 text-sm text-slate-500 flex flex-wrap gap-4 justify-between">
            <span>© {{ date('Y') }} {{ config('app.name') }}</span>
            <nav class="flex flex-wrap gap-4">
                <a href="{{ route('about') }}" class="hover:text-slate-800">Giới thiệu</a>
                <a href="{{ route('aptis') }}" class="hover:text-slate-800">Luyện thi APTIS</a>
                <a href="{{ route('policy.refund') }}" class="hover:text-slate-800">Chính sách hoàn tiền</a>
            </nav>
        </div>
    </footer>
</body>
</html>
