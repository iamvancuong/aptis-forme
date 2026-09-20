@extends('layouts.marketing')

@section('title', 'Ngân hàng đề APTIS - Kho đề luyện thi đầy đủ | nhaiaptis')
@section('meta_description', 'Ngân hàng đề APTIS đầy đủ 4 kỹ năng Reading, Listening, Writing, Speaking, cập nhật liên tục. Xem toàn bộ đề và học thử miễn phí tại nhaiaptis.')

@php
    // [emoji, tên EN, tên VN, class nền header, class nền nút] — dùng class ĐẦY ĐỦ để Tailwind biên dịch.
    $skillMeta = [
        'reading' => ['📖', 'Reading', 'Đọc hiểu', 'bg-emerald-50', 'bg-emerald-600'],
        'listening' => ['🎧', 'Listening', 'Nghe hiểu', 'bg-cyan-50', 'bg-cyan-600'],
        'grammar' => ['✏️', 'Grammar & Vocab', 'Ngữ pháp & từ vựng', 'bg-violet-50', 'bg-violet-600'],
        'writing' => ['📝', 'Writing', 'Viết', 'bg-amber-50', 'bg-amber-600'],
        'speaking' => ['🗣️', 'Speaking', 'Nói', 'bg-blue-50', 'bg-blue-600'],
    ];
    $m = fn ($s) => $skillMeta[$s] ?? ['📘', $s, '', 'bg-slate-50', 'bg-slate-600'];
@endphp

@section('content')
    {{-- Header --}}
    <section class="mx-auto max-w-5xl px-6 pt-12 pb-6 text-center">
        <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-4 py-1.5 text-sm font-semibold text-brand-700 ring-1 ring-brand-100">📚 Ngân hàng đề</span>
        <h1 class="mt-3 text-3xl font-bold text-slate-900 sm:text-4xl">Toàn bộ đề luyện thi APTIS</h1>
        <p class="mx-auto mt-2 max-w-2xl text-slate-500">Kho đề thật, đủ 4 kỹ năng, cập nhật liên tục. Đăng ký để làm bài và được AI chấm điểm.</p>

        <div class="mx-auto mt-6 grid max-w-lg grid-cols-3 gap-3">
            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="text-2xl font-extrabold text-brand-700">{{ $totals['sets'] }}</div>
                <div class="text-xs text-slate-500">bộ đề</div>
            </div>
            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="text-2xl font-extrabold text-brand-700">{{ $totals['questions'] }}</div>
                <div class="text-xs text-slate-500">câu hỏi</div>
            </div>
            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-200">
                <div class="text-2xl font-extrabold text-brand-700">{{ $totals['skills'] }}</div>
                <div class="text-xs text-slate-500">kỹ năng</div>
            </div>
        </div>
    </section>

    {{-- Danh mục theo kỹ năng --}}
    <section class="mx-auto max-w-5xl space-y-6 px-6 pb-16">
        @forelse ($catalog as $group)
            @php([$emoji, $en, $vn, $headerBg, $btnBg] = $m($group['skill']))
            <div class="overflow-hidden rounded-3xl bg-white ring-1 ring-slate-200">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4 {{ $headerBg }}">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ $emoji }}</span>
                        <div>
                            <div class="font-bold text-slate-900">{{ $en }}</div>
                            <div class="text-xs text-slate-500">{{ $vn }} · {{ $group['sets_count'] }} bộ đề · {{ $group['questions_count'] }} câu</div>
                        </div>
                    </div>
                    <a href="{{ route('trial.show', $group['skill']) }}" class="rounded-xl px-4 py-2 text-sm font-semibold text-white hover:opacity-90 {{ $btnBg }}">Học thử miễn phí →</a>
                </div>

                <ul class="grid gap-px bg-slate-100 sm:grid-cols-2">
                    @foreach ($group['sets'] as $set)
                        <li class="flex items-center justify-between gap-3 bg-white px-5 py-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-slate-800">{{ $set['title'] }}</p>
                                <p class="text-xs text-slate-400">
                                    @if ($set['part'])Part {{ $set['part'] }} · @endif{{ $set['questions_count'] }} câu
                                </p>
                            </div>
                            <a href="{{ route('register') }}" class="shrink-0 text-xs font-medium text-slate-400 hover:text-brand-600" title="Đăng ký để làm bài">🔒 Mở</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p class="text-center text-slate-500">Chưa có đề nào được công bố.</p>
        @endforelse
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-3xl px-6 pb-16">
        <div class="rounded-3xl bg-gradient-to-br from-brand-600 to-violet-600 px-8 py-10 text-center text-white">
            <h2 class="text-2xl font-bold">Đăng ký để mở toàn bộ ngân hàng đề</h2>
            <p class="mx-auto mt-2 max-w-lg text-brand-100">Làm bài không giới hạn, chấm điểm tự động và AI nhận xét Writing &amp; Speaking.</p>
            <a href="{{ route('register') }}" class="mt-6 inline-block rounded-2xl bg-white px-7 py-3.5 text-sm font-bold text-brand-700 hover:bg-brand-50">Đăng ký ngay</a>
        </div>
    </section>
@endsection
