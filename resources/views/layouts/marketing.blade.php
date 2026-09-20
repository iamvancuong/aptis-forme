<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name') . ' — Luyện thi APTIS online')</title>
    <meta name="description" content="@yield('meta_description', 'Luyện thi APTIS online: học thử miễn phí, thi thử full đề có tính giờ, AI chấm Writing & Speaking theo tiêu chí APTIS.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta name="robots" content="index, follow">
    @if (config('services.google.site_verification'))
        <meta name="google-site-verification" content="{{ config('services.google.site_verification') }}">
    @endif
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    {{-- Open Graph (Facebook, Zalo…) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="nhaiaptis">
    <meta property="og:locale" content="vi_VN">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:title" content="@yield('title', config('app.name') . ' — Luyện thi APTIS online')">
    <meta property="og:description" content="@yield('meta_description', 'Luyện thi APTIS online: học thử miễn phí, thi thử full đề, AI chấm Writing & Speaking.')">
    <meta property="og:image" content="@yield('og_image', url('images/og-image.png'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', config('app.name') . ' — Luyện thi APTIS online')">
    <meta name="twitter:description" content="@yield('meta_description', 'Luyện thi APTIS online: học thử miễn phí, thi thử full đề, AI chấm Writing & Speaking.')">
    <meta name="twitter:image" content="@yield('og_image', url('images/og-image.png'))">

    {{-- Dữ liệu có cấu trúc: Tổ chức (dựng bằng json_encode để Blade không hiểu nhầm "@context") --}}
    @php
        $orgLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'nhaiaptis',
            'url' => url('/'),
            'logo' => url('favicon.svg'),
            'email' => 'nhaiaptis@gmail.com',
            'description' => 'Nền tảng luyện thi APTIS trực tuyến: luyện tập theo kỹ năng, thi thử full đề và AI chấm Writing & Speaking.',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer support',
                'email' => 'nhaiaptis@gmail.com',
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($orgLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @stack('jsonld')

    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen flex-col font-sans antialiased bg-slate-50 text-slate-900">
    <header class="sticky top-0 z-30 border-b border-slate-200/70 bg-white/80 backdrop-blur">
        <div class="mx-auto max-w-6xl px-6 py-3.5 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                @include('partials.brand-mark', ['size' => 34])
                <span class="text-lg font-extrabold tracking-tight text-slate-900">nhai<span class="text-slate-400">aptis</span></span>
            </a>
            <nav class="flex items-center gap-1 text-sm sm:gap-2">
                <a href="{{ route('aptis') }}" class="hidden rounded-lg px-3 py-2 text-slate-600 hover:text-brand-600 sm:block">Luyện thi APTIS</a>
                <a href="{{ route('catalog') }}" class="hidden rounded-lg px-3 py-2 text-slate-600 hover:text-brand-600 sm:block">Ngân hàng đề</a>
                <a href="{{ route('promo.show') }}" class="rounded-lg px-3 py-2 font-medium text-emerald-600 hover:text-emerald-700">🎁 Học free</a>
                <a href="{{ route('register') }}" class="hidden rounded-lg px-3 py-2 text-slate-600 hover:text-brand-600 sm:block">Đăng ký</a>
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

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-6 py-8 text-sm text-slate-500">
            <div class="flex flex-wrap justify-between gap-4">
                <span>© {{ date('Y') }} nhaiaptis</span>
                <nav class="flex flex-wrap gap-4">
                    <a href="{{ route('about') }}" class="hover:text-slate-800">Giới thiệu</a>
                    <a href="{{ route('aptis') }}" class="hover:text-slate-800">Luyện thi APTIS</a>
                    <a href="{{ route('catalog') }}" class="hover:text-slate-800">Ngân hàng đề</a>
                    <a href="{{ route('policy.refund') }}" class="hover:text-slate-800">Chính sách hoàn tiền</a>
                    <a href="mailto:nhaiaptis@gmail.com" class="hover:text-slate-800">Liên hệ</a>
                </nav>
            </div>
            <p class="mt-4 text-xs leading-relaxed text-slate-400">
                Học liệu được tổng hợp, chọn lọc và sắp xếp từ nhiều nguồn công khai phục vụ mục đích học tập.
                nhaiaptis là nền tảng ôn luyện độc lập, không phải đơn vị tổ chức thi và không liên kết chính thức với British Council.
                “APTIS” thuộc về British Council. Nội dung là đề luyện tập mô phỏng.
                <a href="{{ route('about') }}" class="underline hover:text-slate-600">Xem chi tiết</a>.
            </p>
        </div>
    </footer>
</body>
</html>
