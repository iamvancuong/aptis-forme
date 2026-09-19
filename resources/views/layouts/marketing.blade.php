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
    <header class="bg-white ring-1 ring-slate-200">
        <div class="mx-auto max-w-5xl px-6 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-lg font-bold text-slate-900">{{ config('app.name') }}</a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('register') }}" class="text-slate-600 hover:text-indigo-600">Đăng ký</a>
                <a href="{{ route('login') }}" class="rounded-lg bg-indigo-600 px-3 py-1.5 font-medium text-white hover:bg-indigo-700">Đăng nhập</a>
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
            <a href="{{ route('policy.refund') }}" class="hover:text-slate-800">Chính sách hoàn tiền</a>
        </div>
    </footer>
</body>
</html>
