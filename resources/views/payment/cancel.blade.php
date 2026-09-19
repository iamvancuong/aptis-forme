@extends('layouts.marketing')

@section('title', 'Đã hủy thanh toán')

@section('content')
<section class="mx-auto max-w-lg px-6 py-16 text-center">
    <div class="mx-auto h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center text-3xl">✕</div>
    <h1 class="mt-5 text-2xl font-bold text-slate-900">Đã hủy thanh toán</h1>
    <p class="mt-2 text-slate-600">Đơn {{ $order->order_code }} đã được hủy. Bạn có thể tạo đơn mới bất cứ lúc nào.</p>
    <a href="{{ route('register') }}" class="mt-6 inline-block rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
        Chọn gói lại
    </a>
</section>
@endsection
