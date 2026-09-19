@extends('layouts.marketing')

@section('title', config('app.name') . ' — Luyện thi APTIS online')
@section('meta_description', 'Luyện tập, thi thử và chấm AI cho kỳ thi APTIS. Giá ưu đãi, học mọi lúc.')

@section('content')
    {{-- Hero --}}
    <section class="bg-grid">
        <div class="mx-auto max-w-5xl px-6 pt-20 pb-14 text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-medium text-brand-700 ring-1 ring-brand-200">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Ưu đãi ra mắt phiên bản mới
            </span>
            <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-6xl">
                Luyện thi APTIS <span class="text-gradient">thông minh hơn</span>
            </h1>
            <p class="mx-auto mt-5 max-w-2xl text-lg text-slate-600">
                Luyện tập theo từng kỹ năng, thi thử full đề có tính giờ, và được AI chấm Writing/Speaking —
                tất cả trong một nền tảng.
            </p>
            <div class="mt-8 flex items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="rounded-xl bg-brand-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 hover:bg-brand-700">
                    Bắt đầu ngay →
                </a>
                <a href="#bang-gia" class="rounded-xl bg-white px-6 py-3.5 text-sm font-semibold text-slate-700 ring-1 ring-slate-300 hover:bg-slate-50">
                    Xem bảng giá
                </a>
            </div>

            {{-- Trust stats --}}
            <div class="mx-auto mt-14 grid max-w-2xl grid-cols-3 gap-4">
                @foreach ([['500+', 'Câu hỏi luyện tập'], ['8', 'Phần thi Reading & Listening'], ['AI', 'Chấm Writing & Speaking']] as $stat)
                    <div class="rounded-2xl bg-white/70 p-4 ring-1 ring-slate-200">
                        <div class="text-2xl font-bold text-slate-900">{{ $stat[0] }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $stat[1] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="mx-auto max-w-5xl px-6 py-16">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['🎯', 'Luyện theo kỹ năng', 'Reading, Listening, Grammar — luyện từng part với phản hồi tức thì.'],
                ['⏱️', 'Thi thử tính giờ', 'Mô phỏng phòng thi thật, bốc đề ngẫu nhiên, chấm điểm từng phần.'],
                ['🤖', 'Chấm AI', 'Writing & Speaking được AI chấm chi tiết theo tiêu chí APTIS.'],
                ['📈', 'Theo dõi tiến bộ', 'Lịch sử làm bài, xem lại đáp án và bảng xếp hạng.'],
            ] as $f)
                <div class="rounded-2xl bg-white p-6 ring-1 ring-slate-200 transition hover:ring-brand-300 hover:shadow-sm">
                    <div class="text-2xl">{{ $f[0] }}</div>
                    <h3 class="mt-3 font-semibold text-slate-900">{{ $f[1] }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $f[2] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- High scores --}}
    @if ($highScores->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 pb-4">
            <h2 class="text-center text-sm font-semibold uppercase tracking-widest text-slate-400">Học viên điểm cao</h2>
            <div class="mt-6 flex flex-wrap justify-center gap-8">
                @foreach ($highScores as $hs)
                    <div class="text-center">
                        <div class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-gradient-to-br from-brand-100 to-violet-100 text-lg font-bold text-brand-700 ring-1 ring-brand-200">
                            {{ mb_substr($hs->name, 0, 1) }}
                        </div>
                        <div class="mt-2 text-sm font-medium text-slate-800">{{ $hs->name }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Bảng giá --}}
    <section id="bang-gia" class="mx-auto max-w-5xl px-6 py-16">
        <h2 class="text-center text-3xl font-bold text-slate-900">Bảng giá ưu đãi</h2>
        <p class="mt-2 text-center text-slate-500">Chọn gói phù hợp — cộng dồn thời hạn theo số lượng.</p>

        <div class="mx-auto mt-10 grid max-w-2xl gap-6 sm:grid-cols-2">
            @foreach ($packages as $key => $p)
                <div class="relative rounded-3xl bg-white p-7 ring-1 {{ $p['popular'] ? 'ring-2 ring-brand-500 shadow-xl shadow-brand-600/10' : 'ring-slate-200' }}">
                    @if ($p['popular'])
                        <span class="absolute -top-3 left-7 rounded-full bg-brand-600 px-3 py-0.5 text-xs font-semibold text-white">Phổ biến nhất</span>
                    @endif
                    <div class="text-lg font-semibold text-slate-900">{{ $p['label'] }}</div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-slate-900">{{ number_format($p['price']) }}₫</span>
                        @if (!empty($p['original_price']) && $p['original_price'] > $p['price'])
                            <span class="text-sm text-slate-400 line-through">{{ number_format($p['original_price']) }}₫</span>
                        @endif
                    </div>
                    @if (!empty($p['original_price']) && $p['original_price'] > $p['price'])
                        <span class="mt-2 inline-block rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">
                            Tiết kiệm {{ round((1 - $p['price'] / $p['original_price']) * 100) }}%
                        </span>
                    @endif
                    <div class="mt-2 text-sm text-slate-500">{{ $p['days'] }} ngày sử dụng</div>
                    <a href="{{ route('register', ['goi' => $key]) }}"
                       class="mt-6 block rounded-xl px-4 py-3 text-center text-sm font-semibold {{ $p['popular'] ? 'bg-brand-600 text-white shadow-lg shadow-brand-600/25 hover:bg-brand-700' : 'text-brand-700 ring-1 ring-brand-300 hover:bg-brand-50' }}">
                        Chọn gói này
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Testimonials --}}
    @if ($feedbacks->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 py-16">
            <h2 class="text-center text-3xl font-bold text-slate-900">Học viên nói gì</h2>
            <div class="mt-10 grid gap-6 sm:grid-cols-3">
                @foreach ($feedbacks as $fb)
                    <figure class="rounded-2xl bg-white p-6 ring-1 ring-slate-200">
                        <div class="text-amber-400">★★★★★</div>
                        <blockquote class="mt-3 text-sm text-slate-700">"{{ $fb->content }}"</blockquote>
                        <figcaption class="mt-4 text-sm font-semibold text-slate-900">— {{ $fb->name }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- CTA --}}
    <section class="mx-auto max-w-5xl px-6 pb-20">
        <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-violet-600 px-8 py-12 text-center text-white">
            <h2 class="text-3xl font-bold">Sẵn sàng chinh phục APTIS?</h2>
            <p class="mx-auto mt-2 max-w-xl text-brand-100">Đăng ký hôm nay để nhận ưu đãi ra mắt và bắt đầu luyện ngay.</p>
            <a href="{{ route('register') }}" class="mt-6 inline-block rounded-xl bg-white px-6 py-3.5 text-sm font-semibold text-brand-700 hover:bg-brand-50">
                Đăng ký ngay
            </a>
        </div>
    </section>
@endsection
