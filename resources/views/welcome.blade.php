@extends('layouts.marketing')

@section('title', config('app.name') . ' — Luyện thi APTIS online')
@section('meta_description', 'Luyện tập, thi thử và chấm AI cho kỳ thi APTIS. Học thử miễn phí, giá ưu đãi.')

@section('content')
    {{-- ══════════ HERO ══════════ --}}
    <section class="relative overflow-hidden">
        <div class="blob -left-32 -top-32 h-96 w-96 bg-brand-300"></div>
        <div class="blob -right-24 top-24 h-96 w-96 bg-violet-300"></div>
        <div class="absolute inset-0 bg-grid opacity-60"></div>

        <div class="relative mx-auto grid max-w-6xl items-center gap-12 px-6 pt-16 pb-20 lg:grid-cols-2 lg:pt-24">
            {{-- Cột trái: nội dung --}}
            <div class="animate-fade-up text-center lg:text-left">
                <span class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-1.5 text-sm font-semibold text-brand-700 shadow-sm ring-1 ring-brand-100">
                    🎉 Ưu đãi ra mắt · Giảm đến 50%
                </span>
                <h1 class="mt-6 text-4xl font-extrabold leading-[1.1] tracking-tight text-slate-900 sm:text-6xl">
                    Chinh phục <span class="text-gradient">APTIS</span><br class="hidden sm:block"> thông minh hơn
                </h1>
                <p class="mx-auto mt-5 max-w-lg text-lg text-slate-600 lg:mx-0">
                    Luyện tập theo từng kỹ năng, thi thử full đề có tính giờ và được <b class="text-slate-800">AI chấm Writing &amp; Speaking</b> — tất cả trong một nền tảng.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3 lg:justify-start">
                    <a href="#hoc-thu" class="group rounded-2xl bg-gradient-to-r from-brand-600 to-violet-600 px-7 py-4 text-sm font-bold text-white shadow-xl shadow-brand-600/30 transition hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-brand-600/40">
                        Học thử miễn phí
                        <span class="ml-1 inline-block transition group-hover:translate-x-1">→</span>
                    </a>
                    <a href="{{ route('register') }}" class="rounded-2xl bg-white px-7 py-4 text-sm font-bold text-slate-700 ring-1 ring-slate-200 transition hover:bg-slate-50">
                        Đăng ký ngay
                    </a>
                </div>
                <p class="mt-4 text-sm text-slate-400">✓ Không cần đăng nhập · ✓ Thử mỗi kỹ năng một lượt</p>

                <div class="mt-10 flex items-center justify-center gap-8 lg:justify-start">
                    @foreach ([['500+', 'Câu hỏi'], ['5', 'Kỹ năng'], ['AI', 'Chấm Writing/Speaking']] as $stat)
                        <div>
                            <div class="text-2xl font-extrabold text-slate-900">{{ $stat[0] }}</div>
                            <div class="text-xs text-slate-500">{{ $stat[1] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Cột phải: mockup app --}}
            <div class="animate-fade-up relative mx-auto w-full max-w-md">
                <div class="animate-floaty rounded-3xl bg-white p-6 shadow-2xl shadow-slate-300/50 ring-1 ring-slate-100">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wide text-brand-600">Reading · Part 1</span>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500">Câu 1/4</span>
                    </div>
                    <div class="mt-3 h-1.5 w-full rounded-full bg-slate-100">
                        <div class="h-1.5 w-1/4 rounded-full bg-brand-500"></div>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-slate-800">In the ___, I cycle to work.</p>
                    <div class="mt-3 space-y-2">
                        <div class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600">market</div>
                        <div class="flex items-center justify-between rounded-xl border-2 border-emerald-400 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700">morning <span>✓</span></div>
                        <div class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-600">sun</div>
                    </div>
                    <div class="mt-4 rounded-xl bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-700">✓ Chính xác! Xem giải thích →</div>
                </div>
                {{-- Chip nổi --}}
                <div class="animate-floaty absolute -left-6 top-8 rounded-2xl bg-white px-4 py-3 shadow-xl ring-1 ring-slate-100" style="animation-delay: -2s">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-violet-100 text-violet-600">🤖</span>
                        <div><div class="text-xs font-bold text-slate-800">AI chấm Writing</div><div class="text-[11px] text-slate-400">CEFR B2 · 90%</div></div>
                    </div>
                </div>
                <div class="animate-floaty absolute -right-4 bottom-6 rounded-2xl bg-white px-4 py-3 shadow-xl ring-1 ring-slate-100" style="animation-delay: -4s">
                    <div class="flex items-center gap-2">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-amber-100 text-amber-600">⏱️</span>
                        <div><div class="text-xs font-bold text-slate-800">Thi thử tính giờ</div><div class="text-[11px] text-slate-400">Full đề · 35:00</div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════ FEATURES ══════════ --}}
    <section class="mx-auto max-w-6xl px-6 py-20">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">Mọi thứ để đạt điểm cao</h2>
            <p class="mt-3 text-slate-500">Công cụ luyện thi APTIS toàn diện, thiết kế để bạn tiến bộ nhanh.</p>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['🎯', 'from-sky-500 to-blue-600', 'Luyện theo kỹ năng', 'Reading, Listening, Grammar — phản hồi đúng/sai ngay sau mỗi câu.'],
                ['⏱️', 'from-amber-500 to-orange-600', 'Thi thử tính giờ', 'Mô phỏng phòng thi thật, bốc đề ngẫu nhiên, chấm điểm từng phần.'],
                ['🤖', 'from-violet-500 to-purple-600', 'AI chấm chi tiết', 'Writing &amp; Speaking chấm theo tiêu chí APTIS, xếp hạng CEFR.'],
                ['📈', 'from-emerald-500 to-teal-600', 'Theo dõi tiến bộ', 'Lịch sử làm bài, xem lại đáp án, bảng xếp hạng học viên.'],
            ] as $f)
                <div class="group rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-200/60">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br {{ $f[1] }} text-2xl text-white shadow-lg">{{ $f[0] }}</div>
                    <h3 class="mt-4 font-bold text-slate-900">{{ $f[2] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-slate-500">{!! $f[3] !!}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ══════════ QUY TRÌNH ══════════ --}}
    <section class="mx-auto max-w-6xl px-6 pb-4">
        <div class="grid gap-6 md:grid-cols-3">
            @foreach ([
                ['1', 'Học thử miễn phí', 'Trải nghiệm ngay không cần đăng nhập — mỗi kỹ năng một lượt.'],
                ['2', 'Đăng ký &amp; nhận tài khoản', 'Thanh toán an toàn, tài khoản gửi qua email trong vài phút.'],
                ['3', 'Luyện tập &amp; thi thử', 'Luyện không giới hạn, thi thử full đề, được AI chấm chi tiết.'],
            ] as $step)
                <div class="relative rounded-3xl bg-slate-50 p-6 ring-1 ring-slate-100">
                    <div class="grid h-10 w-10 place-items-center rounded-full bg-brand-600 text-sm font-bold text-white">{{ $step[0] }}</div>
                    <h3 class="mt-4 font-bold text-slate-900">{!! $step[1] !!}</h3>
                    <p class="mt-1.5 text-sm text-slate-500">{!! $step[2] !!}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ══════════ HỌC THỬ ══════════ --}}
    <section id="hoc-thu" class="mx-auto max-w-6xl px-6 py-20">
        <div class="relative overflow-hidden rounded-[2rem] bg-slate-900 p-8 shadow-2xl sm:p-14">
            <div class="blob right-0 top-0 h-72 w-72 bg-brand-600"></div>
            <div class="blob bottom-0 left-1/4 h-72 w-72 bg-violet-600"></div>
            <div class="relative text-center text-white">
                <span class="inline-block rounded-full bg-white/10 px-4 py-1.5 text-sm font-semibold ring-1 ring-white/15">Không cần đăng nhập</span>
                <h2 class="mt-5 text-3xl font-bold sm:text-4xl">Học thử miễn phí ngay</h2>
                <p class="mx-auto mt-3 max-w-xl text-slate-300">Chọn một kỹ năng để làm thử với <b class="text-white">đề thật</b>. Mỗi kỹ năng một lượt — thích thì đăng ký học không giới hạn.</p>
            </div>
            <div class="relative mx-auto mt-10 grid max-w-3xl grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                @foreach ([
                    ['reading', '📖', 'Reading'], ['listening', '🎧', 'Listening'], ['grammar', '✏️', 'Grammar'],
                    ['writing', '📝', 'Writing'], ['speaking', '🗣️', 'Speaking'],
                ] as $sk)
                    <a href="{{ route('trial.show', $sk[0]) }}"
                       class="group flex flex-col items-center gap-2 rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur transition hover:-translate-y-1 hover:bg-white/10 hover:ring-white/30">
                        <span class="text-3xl transition group-hover:scale-110">{{ $sk[1] }}</span>
                        <span class="text-sm font-semibold text-white">{{ $sk[2] }}</span>
                        <span class="text-xs text-brand-300">Làm thử →</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════ HỌC VIÊN ĐIỂM CAO ══════════ --}}
    @if ($highScores->isNotEmpty())
        <section class="mx-auto max-w-5xl px-6 pb-8">
            <p class="text-center text-sm font-semibold uppercase tracking-widest text-slate-400">Học viên tiêu biểu</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-x-10 gap-y-6">
                @foreach ($highScores as $hs)
                    <div class="flex items-center gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-brand-100 to-violet-100 font-bold text-brand-700 ring-1 ring-brand-200">{{ mb_substr($hs->name, 0, 1) }}</div>
                        <span class="text-sm font-medium text-slate-700">{{ $hs->name }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ══════════ BẢNG GIÁ ══════════ --}}
    <section id="bang-gia" class="mx-auto max-w-6xl px-6 py-20">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">Bảng giá ưu đãi</h2>
            <p class="mt-3 text-slate-500">Cộng dồn thời hạn theo số lượng. Hủy bất cứ lúc nào.</p>
        </div>
        <div class="mx-auto mt-12 grid max-w-3xl gap-6 sm:grid-cols-2">
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
                        <li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $p['days'] }} ngày sử dụng</li>
                        <li class="flex gap-2"><span class="text-emerald-500">✓</span> Luyện tập không giới hạn</li>
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

    {{-- ══════════ TESTIMONIALS ══════════ --}}
    @if ($feedbacks->isNotEmpty())
        <section class="mx-auto max-w-6xl px-6 py-16">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">Học viên nói gì</h2>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-3">
                @foreach ($feedbacks as $fb)
                    <figure class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-100">
                        <div class="text-amber-400">★★★★★</div>
                        <blockquote class="mt-3 text-sm leading-relaxed text-slate-700">"{{ $fb->content }}"</blockquote>
                        <figcaption class="mt-5 flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-brand-100 font-bold text-brand-700">{{ mb_substr($fb->name, 0, 1) }}</span>
                            <span class="text-sm font-semibold text-slate-900">{{ $fb->name }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ══════════ CTA CUỐI ══════════ --}}
    <section class="mx-auto max-w-6xl px-6 pb-24">
        <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-brand-600 via-violet-600 to-fuchsia-600 px-8 py-16 text-center text-white shadow-2xl">
            <div class="blob left-10 top-0 h-56 w-56 bg-white/20"></div>
            <div class="relative">
                <h2 class="text-3xl font-bold sm:text-4xl">Sẵn sàng chinh phục APTIS?</h2>
                <p class="mx-auto mt-3 max-w-xl text-white/80">Bắt đầu miễn phí ngay hôm nay — không cần thẻ, không cần đăng nhập.</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <a href="#hoc-thu" class="rounded-2xl bg-white px-7 py-4 text-sm font-bold text-brand-700 hover:bg-brand-50">Học thử miễn phí</a>
                    <a href="{{ route('register') }}" class="rounded-2xl bg-white/10 px-7 py-4 text-sm font-bold text-white ring-1 ring-white/30 hover:bg-white/20">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </section>
@endsection
