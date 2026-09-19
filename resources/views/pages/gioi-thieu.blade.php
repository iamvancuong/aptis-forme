@extends('layouts.marketing')

@section('title', 'Giới thiệu — ' . config('app.name'))
@section('meta_description', 'Giới thiệu nền tảng luyện thi APTIS: luyện tập theo kỹ năng, thi thử và chấm AI.')

@section('content')
<section class="mx-auto max-w-3xl px-6 py-12">
    <h1 class="text-3xl font-bold text-slate-900">Về {{ config('app.name') }}</h1>
    <div class="prose prose-slate mt-6 max-w-none">
        <p>{{ config('app.name') }} là nền tảng luyện thi APTIS trực tuyến, giúp học viên
        luyện tập theo từng kỹ năng (Reading, Listening, Grammar, Writing, Speaking),
        thi thử full đề có tính giờ và nhận phản hồi ngay.</p>
        <p>Ngân hàng đề phong phú, cập nhật liên tục, cùng công nghệ chấm bài bằng AI cho
        phần Viết và Nói giúp bạn tiến bộ nhanh và chủ động ôn luyện mọi lúc.</p>
    </div>
    <a href="{{ route('register') }}" class="mt-8 inline-block rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
        Bắt đầu học ngay
    </a>
</section>
@endsection
