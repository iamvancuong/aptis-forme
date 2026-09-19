@extends('layouts.marketing')

@section('title', 'Kết quả thanh toán')

@section('content')
<section class="mx-auto max-w-lg px-6 py-16 text-center">
    @if ($order->isPaid())
        <div class="mx-auto h-16 w-16 rounded-full bg-emerald-100 flex items-center justify-center text-3xl">✓</div>
        <h1 class="mt-5 text-2xl font-bold text-slate-900">Thanh toán thành công!</h1>
        <p class="mt-2 text-slate-600">
            Thông tin đăng nhập đã được gửi tới email <b>{{ $order->email }}</b>.
            Vui lòng kiểm tra hộp thư (kể cả mục Spam).
        </p>
        <a href="{{ route('login') }}" class="mt-6 inline-block rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
            Đăng nhập ngay
        </a>
    @else
        <div class="mx-auto h-16 w-16 rounded-full bg-amber-100 flex items-center justify-center text-3xl">⏳</div>
        <h1 class="mt-5 text-2xl font-bold text-slate-900">Đang xác nhận thanh toán</h1>
        <p class="mt-2 text-slate-600">
            Nếu bạn vừa thanh toán, hệ thống sẽ cập nhật trong giây lát và gửi email cho bạn.
        </p>
    @endif
</section>
@endsection
