@extends('layouts.marketing')

@section('title', config('app.name') . ' — Luyện thi APTIS online')
@section('meta_description', 'Luyện tập, thi thử và chấm AI cho kỳ thi APTIS. Giá ưu đãi, học mọi lúc.')

@section('content')
    {{-- Hero --}}
    <section class="mx-auto max-w-5xl px-6 pt-16 pb-12 text-center">
        <span class="inline-block rounded-full bg-indigo-50 px-4 py-1.5 text-sm font-medium text-indigo-700 ring-1 ring-indigo-200">
            Ưu đãi ra mắt phiên bản mới
        </span>
        <h1 class="mt-5 text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">
            Luyện thi APTIS thông minh hơn
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-600">
            Luyện tập theo từng kỹ năng, thi thử full đề có tính giờ, và được AI chấm Writing/Speaking —
            tất cả trong một nền tảng.
        </p>
        <div class="mt-8 flex items-center justify-center gap-3">
            <a href="{{ route('register') }}" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                Bắt đầu ngay
            </a>
            <a href="#bang-gia" class="rounded-xl px-6 py-3 text-sm font-semibold text-slate-700 ring-1 ring-slate-300 hover:bg-white">
                Xem bảng giá
            </a>
        </div>
    </section>

    {{-- High scores (đọc từ db1) --}}
    @if ($highScores->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 py-8">
            <h2 class="text-center text-sm font-semibold uppercase tracking-widest text-slate-400">Học viên điểm cao</h2>
            <div class="mt-6 flex flex-wrap justify-center gap-6">
                @foreach ($highScores as $hs)
                    <div class="text-center">
                        <div class="mx-auto h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-lg font-bold text-indigo-700">
                            {{ mb_substr($hs->name, 0, 1) }}
                        </div>
                        <div class="mt-2 text-sm font-medium text-slate-800">{{ $hs->name }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Bảng giá --}}
    <section id="bang-gia" class="mx-auto max-w-5xl px-6 py-12">
        <h2 class="text-center text-3xl font-bold text-slate-900">Bảng giá</h2>
        <p class="mt-2 text-center text-slate-500">Chọn gói phù hợp — cộng dồn thời hạn theo số lượng.</p>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 max-w-2xl mx-auto">
            @foreach ($packages as $key => $p)
                <div class="relative rounded-2xl bg-white p-6 ring-1 {{ $p['popular'] ? 'ring-2 ring-indigo-500' : 'ring-slate-200' }}">
                    @if ($p['popular'])
                        <span class="absolute -top-3 left-6 rounded-full bg-indigo-600 px-3 py-0.5 text-xs font-semibold text-white">Phổ biến</span>
                    @endif
                    <div class="text-lg font-semibold text-slate-900">{{ $p['label'] }}</div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-bold text-slate-900">{{ number_format($p['price']) }}₫</span>
                        @if (!empty($p['original_price']) && $p['original_price'] > $p['price'])
                            <span class="text-sm text-slate-400 line-through">{{ number_format($p['original_price']) }}₫</span>
                        @endif
                    </div>
                    <div class="mt-1 text-sm text-slate-500">{{ $p['days'] }} ngày sử dụng</div>
                    <a href="{{ route('register', ['goi' => $key]) }}"
                       class="mt-6 block rounded-xl px-4 py-2.5 text-center text-sm font-semibold {{ $p['popular'] ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'text-indigo-700 ring-1 ring-indigo-300 hover:bg-indigo-50' }}">
                        Chọn gói này
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Testimonials (đọc từ db1) --}}
    @if ($feedbacks->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 py-12">
            <h2 class="text-center text-3xl font-bold text-slate-900">Học viên nói gì</h2>
            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                @foreach ($feedbacks as $fb)
                    <figure class="rounded-2xl bg-white p-6 ring-1 ring-slate-200">
                        <blockquote class="text-sm text-slate-700">"{{ $fb->content }}"</blockquote>
                        <figcaption class="mt-4 text-sm font-semibold text-slate-900">— {{ $fb->name }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif
@endsection
