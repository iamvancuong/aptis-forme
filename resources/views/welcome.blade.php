@extends('layouts.marketing')

@section('title', config('app.name') . ' — Luyện thi APTIS online')
@section('meta_description', 'Luyện thi APTIS đạt điểm cao: học thử miễn phí, thi thử full đề, AI chấm Writing & Speaking. Giá ưu đãi.')

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
                    <a href="#hoc-thu" class="group rounded-2xl bg-gradient-to-r from-brand-600 to-violet-600 px-7 py-4 text-sm font-bold text-white shadow-xl shadow-brand-600/30 transition hover:-translate-y-0.5">
                        Học thử miễn phí <span class="ml-1 inline-block transition group-hover:translate-x-1">→</span>
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

            <div class="animate-fade-up relative mx-auto w-full max-w-md">
                <div class="animate-floaty rounded-3xl bg-white p-6 shadow-2xl shadow-slate-300/50 ring-1 ring-slate-100">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wide text-brand-600">Reading · Part 1</span>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">Câu 1/4</span>
                    </div>
                    <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100"><div class="h-1.5 w-1/4 rounded-full bg-brand-500"></div></div>
                    <p class="mt-4 text-sm font-semibold text-slate-800">In the ___, I cycle to work.</p>
                    <div class="mt-3 space-y-2">
                        <div class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600">market</div>
                        <div class="flex items-center justify-between rounded-xl border-2 border-emerald-400 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700">morning <span>✓</span></div>
                        <div class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600">sun</div>
                    </div>
                    <div class="mt-4 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-700">✓ Chính xác! Xem giải thích →</div>
                </div>
                <div class="animate-floaty absolute -left-6 top-8 rounded-2xl bg-white px-4 py-3 shadow-xl ring-1 ring-slate-100" style="animation-delay: -2s">
                    <div class="flex items-center gap-2"><span class="grid h-8 w-8 place-items-center rounded-lg bg-violet-100 text-violet-600">🤖</span><div><div class="text-xs font-bold text-slate-800">AI chấm Writing</div><div class="text-[11px] text-slate-400">CEFR B2 · 90%</div></div></div>
                </div>
                <div class="animate-floaty absolute -right-4 bottom-6 rounded-2xl bg-white px-4 py-3 shadow-xl ring-1 ring-slate-100" style="animation-delay: -4s">
                    <div class="flex items-center gap-2"><span class="grid h-8 w-8 place-items-center rounded-lg bg-amber-100 text-amber-600">⏱️</span><div><div class="text-xs font-bold text-slate-800">Thi thử tính giờ</div><div class="text-[11px] text-slate-400">Full đề · 35:00</div></div></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════ HỌC THỬ (đồng bộ màu brand) ══════════ --}}
    <section id="hoc-thu" class="mx-auto max-w-6xl px-6 py-10">
        <div class="rounded-[2rem] bg-gradient-to-br from-brand-50 to-violet-50 p-8 ring-1 ring-brand-100 sm:p-10">
            <div class="text-center">
                <h2 class="text-2xl font-bold text-slate-900 sm:text-3xl">Học thử miễn phí — đề thật</h2>
                <p class="mx-auto mt-2 max-w-xl text-sm text-slate-600">Không cần đăng nhập. Chọn một kỹ năng để làm thử ngay, mỗi kỹ năng một lượt.</p>
            </div>
            <div class="mx-auto mt-7 grid max-w-3xl grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ([
                    ['reading', '📖', 'Reading'], ['listening', '🎧', 'Listening'], ['grammar', '✏️', 'Grammar'],
                    ['writing', '📝', 'Writing'], ['speaking', '🗣️', 'Speaking'],
                ] as $sk)
                    <a href="{{ route('trial.show', $sk[0]) }}"
                       class="group flex flex-col items-center gap-2 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 transition hover:-translate-y-1 hover:shadow-lg hover:ring-brand-300">
                        <span class="text-3xl transition group-hover:scale-110">{{ $sk[1] }}</span>
                        <span class="text-sm font-semibold text-slate-800">{{ $sk[2] }}</span>
                        <span class="text-xs font-medium text-brand-600">Làm thử →</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════ FEATURES (gọn, 1 hàng) ══════════ --}}
    <section class="mx-auto max-w-6xl px-6 py-10">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['🎯', 'from-sky-500 to-blue-600', 'Luyện đúng trọng tâm', 'Phản hồi đúng/sai ngay sau mỗi câu.'],
                ['⏱️', 'from-amber-500 to-orange-600', 'Thi thử sát đề thật', 'Full đề có tính giờ, chấm từng phần.'],
                ['🤖', 'from-violet-500 to-purple-600', 'AI chấm chi tiết', 'Writing & Speaking theo tiêu chí APTIS.'],
                ['📈', 'from-emerald-500 to-teal-600', 'Theo dõi tiến bộ', 'Lịch sử, đáp án, bảng xếp hạng.'],
            ] as $f)
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100 transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br {{ $f[1] }} text-xl text-white shadow-md">{{ $f[0] }}</div>
                    <h3 class="mt-3 font-bold text-slate-900">{{ $f[2] }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $f[3] }}</p>
                </div>
            @endforeach
        </div>
    </section>

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

    {{-- ══════════ SOCIAL PROOF (gọn) ══════════ --}}
    @if ($feedbacks->isNotEmpty() || $highScores->isNotEmpty())
        <section class="mx-auto max-w-6xl px-6 pb-4">
            @if ($feedbacks->isNotEmpty())
                <div class="grid gap-6 sm:grid-cols-3">
                    @foreach ($feedbacks as $fb)
                        <figure class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                            <div class="text-amber-400">★★★★★</div>
                            <blockquote class="mt-3 text-sm leading-relaxed text-slate-700">"{{ $fb->content }}"</blockquote>
                            <figcaption class="mt-4 flex items-center gap-3">
                                <span class="grid h-9 w-9 place-items-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">{{ mb_substr($fb->name, 0, 1) }}</span>
                                <span class="text-sm font-semibold text-slate-900">{{ $fb->name }}</span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    {{-- ══════════ CTA CUỐI ══════════ --}}
    <section class="mx-auto max-w-5xl px-6 py-14">
        <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-brand-600 via-violet-600 to-fuchsia-600 px-8 py-14 text-center text-white shadow-2xl">
            <div class="blob left-10 top-0 h-56 w-56 bg-white/20"></div>
            <div class="relative">
                <h2 class="text-3xl font-bold sm:text-4xl">Bắt đầu chinh phục APTIS hôm nay</h2>
                <p class="mx-auto mt-3 max-w-xl text-white/80">Học thử miễn phí — không cần thẻ, không cần đăng nhập.</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="#hoc-thu" class="rounded-2xl bg-white px-7 py-4 text-sm font-bold text-brand-700 hover:bg-brand-50">Học thử miễn phí</a>
                    <a href="{{ route('register') }}" class="rounded-2xl bg-white/10 px-7 py-4 text-sm font-bold text-white ring-1 ring-white/30 hover:bg-white/20">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </section>
@endsection
