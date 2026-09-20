@extends('layouts.marketing')

@section('title', config('app.name') . ' — Luyện thi APTIS online')
@section('meta_description', 'Luyện thi APTIS đạt điểm cao: học thử miễn phí, thi thử full đề, AI chấm Writing & Speaking. Giá ưu đãi.')

@push('jsonld')
@php
    $courseLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => 'Luyện thi APTIS online',
        'description' => 'Luyện thi APTIS đủ 4 kỹ năng Reading, Listening, Writing, Speaking: học thử miễn phí, thi thử full đề có tính giờ và AI chấm Writing & Speaking theo tiêu chí APTIS.',
        'provider' => ['@type' => 'Organization', 'name' => 'nhaiaptis', 'sameAs' => url('/')],
        'offers' => array_map(fn ($p) => [
            '@type' => 'Offer',
            'name' => $p['label'],
            'price' => (string) (int) $p['price'],
            'priceCurrency' => 'VND',
            'category' => 'Paid',
            'url' => route('register'),
        ], array_values($packages)),
    ];

    $faqLd = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            ['@type' => 'Question', 'name' => 'Luyện thi APTIS trên nhaiaptis có gì?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Luyện tập theo từng kỹ năng (Reading, Listening, Writing, Speaking), thi thử full đề có tính giờ và được AI chấm Writing & Speaking theo tiêu chí APTIS.']],
            ['@type' => 'Question', 'name' => 'Có được học thử miễn phí không?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Có. Bạn học thử ngay mỗi kỹ năng bằng đề thật mà không cần đăng nhập hay nhập thẻ.']],
            ['@type' => 'Question', 'name' => 'AI chấm Writing và Speaking như thế nào?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'AI phiên âm bài nói và phân tích bài viết, cho điểm cùng nhận xét chi tiết theo các tiêu chí CEFR/APTIS như ngữ pháp, từ vựng, độ trôi chảy và hoàn thành yêu cầu.']],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($courseLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($faqLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endpush

@section('content')
    {{-- ══════════ HERO ══════════ --}}
    <section class="relative overflow-hidden">
        <div class="blob -left-32 -top-32 h-80 w-80 bg-brand-300"></div>
        <div class="blob -right-24 top-16 h-80 w-80 bg-violet-300"></div>
        <div class="absolute inset-0 bg-grid opacity-50"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-10 px-6 pt-14 pb-16 lg:grid-cols-2 lg:pt-20">
            <div class="animate-fade-up text-center lg:text-left">
                <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-brand-700 shadow-sm ring-1 ring-brand-100">
                    🎯 Lộ trình đạt điểm cao APTIS
                </span>
                <h1 class="mt-5 text-4xl font-extrabold leading-[1.1] tracking-tight text-slate-900 sm:text-6xl">
                    Đạt điểm <span class="text-gradient">APTIS</span> mơ ước
                </h1>
                <p class="mx-auto mt-5 max-w-lg text-lg text-slate-600 lg:mx-0">
                    Luyện đúng trọng tâm, thi thử sát đề thật và được <b class="text-slate-800">AI chấm Writing &amp; Speaking</b> theo tiêu chí APTIS — tiến bộ nhanh, chắc điểm.
                </p>
                <div class="mt-7 flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                    <a href="{{ route('trial.show', 'reading') }}" class="group rounded-2xl bg-gradient-to-r from-brand-600 to-violet-600 px-7 py-4 text-sm font-bold text-white shadow-xl shadow-brand-600/30 transition hover:-translate-y-0.5">
                        Học thử ngay <span class="ml-1 inline-block transition group-hover:translate-x-1">→</span>
                    </a>
                    <a href="#bang-gia" class="rounded-2xl bg-white px-7 py-4 text-sm font-bold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-50">
                        Xem bảng giá
                    </a>
                </div>
                <div class="mt-7 flex items-center justify-center gap-8 lg:justify-start">
                    @foreach ([['500+', 'Câu hỏi thật'], ['5', 'Kỹ năng'], ['AI', 'Chấm W/S']] as $stat)
                        <div><div class="text-2xl font-extrabold text-slate-900">{{ $stat[0] }}</div><div class="text-xs text-slate-500">{{ $stat[1] }}</div></div>
                    @endforeach
                </div>
            </div>

            {{-- Cột phải: KHU HỌC THỬ (bấm vào học ngay) --}}
            <div id="hoc-thu" class="animate-fade-up mx-auto w-full max-w-md">
                <div class="rounded-3xl bg-white p-6 shadow-2xl shadow-slate-300/50 ring-1 ring-slate-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Học thử miễn phí</h3>
                            <p class="text-xs text-slate-500">Đề thật · không cần đăng nhập</p>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Miễn phí</span>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach ([
                            ['reading', '📖', 'Reading', 'Đọc hiểu', 'bg-sky-100'],
                            ['listening', '🎧', 'Listening', 'Nghe hiểu', 'bg-violet-100'],
                            ['grammar', '✏️', 'Grammar', 'Ngữ pháp & từ vựng', 'bg-emerald-100'],
                            ['writing', '📝', 'Writing', 'AI chấm bài viết', 'bg-amber-100'],
                            ['speaking', '🗣️', 'Speaking', 'AI chấm phát âm', 'bg-rose-100'],
                        ] as $sk)
                            <a href="{{ route('trial.show', $sk[0]) }}"
                               class="group flex items-center gap-3 rounded-2xl border border-slate-100 p-3 transition hover:border-brand-200 hover:bg-brand-50">
                                <span class="grid h-11 w-11 flex-shrink-0 place-items-center rounded-xl {{ $sk[4] }} text-xl">{{ $sk[1] }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-semibold text-slate-900">{{ $sk[2] }}</span>
                                    <span class="block text-xs text-slate-400">{{ $sk[3] }}</span>
                                </span>
                                <span class="flex-shrink-0 rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition group-hover:bg-brand-600 group-hover:text-white">Làm thử →</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════ NGÂN HÀNG ĐỀ (uy tín) ══════════ --}}
    @if (($catalogStats['sets'] ?? 0) > 0)
        <section class="mx-auto max-w-5xl px-6 py-10">
            <div class="flex flex-wrap items-center justify-between gap-6 rounded-3xl bg-slate-900 px-8 py-8 text-white">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-brand-300">Ngân hàng đề</p>
                    <h2 class="mt-1 text-2xl font-bold">Kho đề thật, cập nhật liên tục</h2>
                    <p class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-sm text-slate-300">
                        <span><b class="text-xl font-extrabold text-white">{{ $catalogStats['sets'] }}</b> bộ đề</span>
                        <span><b class="text-xl font-extrabold text-white">{{ $catalogStats['questions'] }}</b> câu hỏi</span>
                        <span><b class="text-xl font-extrabold text-white">{{ $catalogStats['skills'] }}</b> kỹ năng</span>
                    </p>
                </div>
                <a href="{{ route('catalog') }}" class="rounded-2xl bg-white px-6 py-3 text-sm font-bold text-slate-900 hover:bg-slate-100">Xem toàn bộ đề →</a>
            </div>
        </section>
    @endif

    {{-- ══════════ DEMO GIAO DIỆN LUYỆN THI ══════════ --}}
    @include('partials.demo-tabs')

    {{-- ══════════ BẢNG GIÁ ══════════ --}}
    <section id="bang-gia" class="mx-auto max-w-4xl px-6 py-12">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-slate-900">Bảng giá ưu đãi</h2>
            <p class="mt-2 text-slate-500">Học không giới hạn tới ngày thi. Cộng dồn thời hạn theo số lượng.</p>
        </div>
        <div class="mx-auto mt-10 grid max-w-3xl gap-6 sm:grid-cols-2">
            @foreach ($packages as $key => $p)
                <div class="relative overflow-hidden rounded-[1.75rem] p-8 {{ $p['popular'] ? 'gradient-border shadow-2xl shadow-brand-600/10' : 'bg-white ring-1 ring-slate-200' }}">
                    @if ($p['popular'])
                        <span class="absolute right-6 top-6 rounded-full bg-gradient-to-r from-brand-600 to-violet-600 px-3 py-1 text-xs font-bold text-white">Phổ biến nhất</span>
                    @endif
                    <div class="text-lg font-bold text-slate-900">{{ $p['label'] }}</div>
                    <div class="mt-4 flex items-end gap-2">
                        <span class="text-4xl font-extrabold text-slate-900">{{ number_format($p['price']) }}₫</span>
                        @if (!empty($p['original_price']) && $p['original_price'] > $p['price'])
                            <span class="mb-1 text-sm text-slate-400 line-through">{{ number_format($p['original_price']) }}₫</span>
                        @endif
                    </div>
                    @if (!empty($p['original_price']) && $p['original_price'] > $p['price'])
                        <span class="mt-2 inline-block rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Tiết kiệm {{ round((1 - $p['price'] / $p['original_price']) * 100) }}%</span>
                    @endif
                    <ul class="mt-6 space-y-2.5 text-sm text-slate-600">
                        <li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $p['days'] }} ngày · luyện không giới hạn</li>
                        <li class="flex gap-2"><span class="text-emerald-500">✓</span> Thi thử full đề có tính giờ</li>
                        <li class="flex gap-2"><span class="text-emerald-500">✓</span> AI chấm Writing &amp; Speaking</li>
                    </ul>
                    <a href="{{ route('register', ['goi' => $key]) }}"
                       class="mt-8 block rounded-2xl px-4 py-3.5 text-center text-sm font-bold {{ $p['popular'] ? 'bg-gradient-to-r from-brand-600 to-violet-600 text-white shadow-lg shadow-brand-600/25 hover:opacity-95' : 'text-brand-700 ring-1 ring-brand-300 hover:bg-brand-50' }}">
                        Chọn gói này
                    </a>
                </div>
            @endforeach
        </div>
    </section>

@endsection
