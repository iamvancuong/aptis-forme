@extends('layouts.marketing')

@section('title', 'Đăng ký — ' . config('app.name'))

@section('content')
<section class="mx-auto max-w-lg px-6 py-12">
    <h1 class="text-2xl font-bold text-slate-900">Đăng ký tài khoản</h1>
    <p class="mt-1 text-sm text-slate-500">Chọn gói, nhập email — tài khoản được cấp tự động sau khi thanh toán.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-5 rounded-2xl bg-white p-6 ring-1 ring-slate-200"
          x-data="{ pkg: '{{ $selected }}', qty: 1, packages: @js($packages) }">
        @csrf
        <input type="hidden" name="sale" value="{{ $sale }}">

        {{-- Chọn gói --}}
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach ($packages as $key => $p)
                <label class="cursor-pointer rounded-xl border p-4"
                       :class="pkg === '{{ $key }}' ? 'border-indigo-500 ring-2 ring-indigo-200' : 'border-slate-200'">
                    <input type="radio" name="package" value="{{ $key }}" class="sr-only" x-model="pkg">
                    <div class="font-semibold text-slate-900">{{ $p['label'] }}</div>
                    <div class="mt-1 text-lg font-bold text-slate-900">{{ number_format($p['price']) }}₫</div>
                    <div class="text-xs text-slate-500">{{ $p['days'] }} ngày</div>
                </label>
            @endforeach
        </div>

        {{-- Số lượng --}}
        <div>
            <label class="block text-sm font-medium text-slate-700">Số lượng (cộng dồn thời hạn)</label>
            <input type="number" name="quantity" min="1" x-model.number="qty"
                   class="mt-1 w-28 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none">
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-slate-700">Email nhận tài khoản</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 focus:outline-none">
        </div>

        {{-- Tổng tiền --}}
        <div class="rounded-lg bg-slate-50 px-4 py-3 text-sm">
            Tổng thanh toán:
            <span class="font-bold text-indigo-700" x-text="new Intl.NumberFormat('vi-VN').format(packages[pkg].price * Math.max(1, qty)) + '₫'"></span>
        </div>

        @if ($sale)
            <p class="text-xs text-emerald-600">Áp dụng mã giới thiệu: <b>{{ $sale }}</b></p>
        @endif

        <button type="submit" class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
            Tiếp tục thanh toán
        </button>
    </form>
</section>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.0/dist/cdn.min.js" defer></script>
@endsection
