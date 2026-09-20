@extends('layouts.marketing')

@section('title', 'Giới thiệu nhaiaptis - Nền tảng luyện thi APTIS online')
@section('meta_description', 'nhaiaptis là nền tảng luyện thi APTIS trực tuyến: luyện theo kỹ năng, thi thử full đề và AI chấm Writing & Speaking. Học liệu tổng hợp, cập nhật liên tục.')

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

    {{-- Nguồn nội dung & miễn trừ trách nhiệm --}}
    <div class="mt-10 rounded-2xl bg-slate-50 p-6 ring-1 ring-slate-200">
        <h2 class="text-lg font-bold text-slate-900">Nguồn nội dung &amp; Miễn trừ trách nhiệm</h2>
        <div class="prose prose-slate mt-3 max-w-none text-sm text-slate-600">
            <p>Học liệu trên {{ config('app.name') }} được <b>biên soạn, tổng hợp, chọn lọc và sắp xếp lại từ nhiều nguồn tài liệu luyện thi công khai</b> cùng kinh nghiệm giảng dạy, nhằm phục vụ mục đích <b>học tập và ôn luyện</b>. Chúng tôi đầu tư công sức hệ thống hoá, chuẩn hoá định dạng và bổ sung phần chấm điểm/nhận xét để tạo nên trải nghiệm luyện thi riêng của nền tảng.</p>
            <p>“APTIS” là kỳ thi và thương hiệu thuộc về <b>British Council</b>. {{ config('app.name') }} là nền tảng ôn luyện <b>độc lập</b>, <b>không phải là đơn vị tổ chức thi</b> và <b>không đại diện, không liên kết chính thức</b> với British Council hay bất kỳ tổ chức nào. Đề trên nền tảng là <b>đề luyện tập mô phỏng</b>, không phải đề thi thật.</p>
            <p>Mọi nhãn hiệu, tên gọi, hình ảnh (nếu có) thuộc quyền của chủ sở hữu tương ứng. Nếu bạn là chủ sở hữu nội dung và cho rằng có nội dung chưa phù hợp về bản quyền, vui lòng liên hệ để chúng tôi <b>rà soát và gỡ bỏ trong thời gian sớm nhất</b>.</p>
        </div>
    </div>

    <a href="{{ route('register') }}" class="mt-8 inline-block rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
        Bắt đầu học ngay
    </a>
</section>
@endsection
