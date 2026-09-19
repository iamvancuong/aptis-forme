@extends('layouts.marketing')

@section('title', 'Thanh toán đơn ' . $order->order_code)

@section('content')
<section class="mx-auto max-w-lg px-6 py-12">
    <div class="rounded-2xl bg-white p-6 ring-1 ring-slate-200">
        <h1 class="text-xl font-bold text-slate-900">Xác nhận đơn hàng</h1>

        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-slate-500">Mã đơn</dt><dd class="font-medium">{{ $order->order_code }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Gói</dt><dd class="font-medium">{{ $package['label'] ?? $order->package }} × {{ $order->quantity }}</dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd class="font-medium">{{ $order->email }}</dd></div>
            <div class="flex justify-between border-t pt-2"><dt class="text-slate-500">Tổng tiền</dt><dd class="font-bold text-indigo-700">{{ number_format($order->amount) }}₫</dd></div>
        </dl>

        @if ($state === 'fake')
            <div class="mt-6 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-700">
                🧪 Chế độ giả lập (PAYOS_FAKE). Bấm nút dưới để mô phỏng thanh toán thành công.
            </div>
            <a href="{{ route('payment.dev-fulfill', $order) }}"
               class="mt-4 block rounded-xl bg-emerald-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-emerald-700">
                Giả lập đã thanh toán
            </a>
        @elseif ($state === 'unconfigured')
            <div class="mt-6 rounded-lg bg-slate-50 px-4 py-3 text-sm text-slate-600">
                Cổng thanh toán đang được hoàn thiện. Vui lòng liên hệ để được hỗ trợ.
            </div>
        @elseif ($state === 'error')
            <div class="mt-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                Cổng thanh toán tạm thời bận. Vui lòng thử lại.
            </div>
            <a href="{{ $retryUrl }}" class="mt-4 block rounded-xl bg-indigo-600 px-4 py-3 text-center text-sm font-semibold text-white hover:bg-indigo-700">
                Thử lại
            </a>
        @endif
    </div>
</section>
@endsection
