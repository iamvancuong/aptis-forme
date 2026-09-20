@extends('layouts.marketing')

@section('title', 'Nhập mã — học thử miễn phí ' . $freeDays . ' ngày | nhaiaptis')
@section('meta_description', 'Nhập mã khuyến mãi để nhận tài khoản luyện thi APTIS miễn phí ' . $freeDays . ' ngày trên nhaiaptis. Mỗi email 1 lần.')

@section('content')
<section class="mx-auto max-w-md px-6 py-12">
    <div class="text-center">
        <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-4 py-1.5 text-sm font-semibold text-brand-700 ring-1 ring-brand-100">🎁 Ưu đãi ra mắt</span>
        <h1 class="mt-3 text-2xl font-bold text-slate-900">Học thử miễn phí {{ $freeDays }} ngày</h1>
        <p class="mt-2 text-sm text-slate-500">Nhập mã khuyến mãi để nhận tài khoản. Mật khẩu sẽ gửi vào email của bạn.</p>
    </div>

    @if (! $enabled)
        <div class="mt-6 rounded-2xl bg-slate-100 p-5 text-center text-slate-600 ring-1 ring-slate-200">
            Chương trình tạm đóng. Vui lòng quay lại sau nhé.
        </div>
    @else
        <form method="POST" action="{{ route('promo.redeem') }}" id="promoForm" class="mt-6 space-y-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            @csrf
            <input type="hidden" name="fp" id="fp">

            <div>
                <label class="block text-sm font-medium text-slate-700">Email nhận tài khoản</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                       placeholder="ban@example.com"
                       class="mt-1 w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Mã khuyến mãi</label>
                <input type="text" name="code" value="{{ old('code') }}" required autocapitalize="characters"
                       placeholder="Nhập mã bạn nhận được"
                       class="mt-1 w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm uppercase shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-600 to-violet-600 py-3 text-sm font-bold text-white shadow-lg shadow-brand-600/25 hover:opacity-95">
                Nhận tài khoản miễn phí
            </button>
        </form>

        {{-- Cảnh báo chống gian lận (răn đe + có check thật) --}}
        <div class="mt-4 rounded-2xl bg-rose-50 p-4 text-sm text-rose-700 ring-1 ring-rose-200">
            <p class="font-bold">⚠️ Lưu ý chống gian lận</p>
            <ul class="mt-1.5 list-disc space-y-1 pl-5 text-rose-600">
                <li>Mỗi <b>email</b> chỉ nhận ưu đãi <b>1 lần duy nhất</b>.</li>
                <li>Hệ thống <b>tự động phát hiện</b> hành vi tạo nhiều tài khoản qua <b>thiết bị và địa chỉ mạng (IP)</b>.</li>
                <li>Cố tình lách để né phí sẽ bị <b>khóa vĩnh viễn toàn bộ tài khoản liên quan</b> và <b>không được hoàn tiền</b>.</li>
            </ul>
        </div>

        <p class="mt-4 text-center text-sm text-slate-500">
            Hết {{ $freeDays }} ngày, <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:underline">đăng ký gói</a> để tiếp tục học.
        </p>
    @endif
</section>

<script>
    // Fingerprint nhẹ (không đọc được MAC/wifi — chỉ tín hiệu trình duyệt) để giới hạn mềm.
    (function () {
        var f = document.getElementById('fp');
        if (!f) return;
        try {
            var parts = [
                navigator.userAgent,
                navigator.language,
                screen.width + 'x' + screen.height + 'x' + (screen.colorDepth || ''),
                new Date().getTimezoneOffset(),
                navigator.hardwareConcurrency || '',
                navigator.platform || '',
            ];
            f.value = parts.join('|').slice(0, 250);
        } catch (e) { /* bỏ qua */ }
    })();
</script>
@endsection
