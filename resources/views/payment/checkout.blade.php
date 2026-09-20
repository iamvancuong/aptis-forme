<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thanh toán — nhaiaptis</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body class="min-h-full bg-slate-50 font-sans antialiased text-slate-900">
    <div class="mx-auto flex min-h-screen max-w-md flex-col justify-center px-6 py-10">
        <div class="mb-6 flex items-center justify-center gap-2">
            @include('partials.brand-mark', ['size' => 32])
            <span class="text-lg font-extrabold tracking-tight">nhai<span class="text-slate-400">aptis</span></span>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="text-center">
                <p class="text-sm text-slate-500">Thanh toán gói <b class="text-slate-800">{{ $package['label'] ?? $order->package }}</b></p>
                <p class="mt-1 text-3xl font-extrabold text-brand-700">{{ number_format($amount) }}₫</p>
            </div>

            {{-- QR --}}
            <div class="mt-5 flex justify-center">
                <div class="rounded-2xl bg-white p-3 ring-1 ring-slate-200">
                    <div id="qr" class="h-[220px] w-[220px]"></div>
                </div>
            </div>
            <p class="mt-3 text-center text-sm text-slate-500">Mở app ngân hàng → <b>quét mã QR</b> để thanh toán</p>

            {{-- Thông tin chuyển khoản thủ công (KHÔNG hiển thị tên chủ tài khoản) --}}
            <div class="mt-5 space-y-2 rounded-2xl bg-slate-50 p-4 text-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-slate-500">Số tài khoản</span>
                    <span class="flex items-center gap-2"><b class="font-mono" id="acc">{{ $accountNumber }}</b>
                        <button type="button" class="copy rounded bg-white px-2 py-0.5 text-xs text-brand-600 ring-1 ring-slate-200" data-t="{{ $accountNumber }}">Sao chép</button></span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-slate-500">Số tiền</span>
                    <span class="flex items-center gap-2"><b>{{ number_format($amount) }}₫</b>
                        <button type="button" class="copy rounded bg-white px-2 py-0.5 text-xs text-brand-600 ring-1 ring-slate-200" data-t="{{ $amount }}">Sao chép</button></span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span class="text-slate-500">Nội dung</span>
                    <span class="flex items-center gap-2"><b class="font-mono">{{ $content }}</b>
                        <button type="button" class="copy rounded bg-white px-2 py-0.5 text-xs text-brand-600 ring-1 ring-slate-200" data-t="{{ $content }}">Sao chép</button></span>
                </div>
            </div>
            <p class="mt-3 rounded-xl bg-amber-50 p-3 text-xs text-amber-700 ring-1 ring-amber-100">
                ⚠️ Nhập <b>đúng số tiền</b> và <b>đúng nội dung</b> để hệ thống tự xác nhận. Tài khoản sẽ được tạo &amp; gửi mật khẩu về email sau khi thanh toán.
            </p>

            <div class="mt-4 flex items-center justify-center gap-2 text-sm text-slate-500">
                <svg class="h-4 w-4 animate-spin text-brand-500" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"/></svg>
                Đang chờ thanh toán…
            </div>

            <div class="mt-4 flex items-center justify-between text-xs">
                <a href="{{ route('home') }}" class="text-slate-400 hover:text-slate-600">← Hủy</a>
                @if ($checkoutUrl)
                    <a href="{{ $checkoutUrl }}" class="text-slate-400 hover:text-brand-600">QR lỗi? Mở trang khác</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Overlay thành công --}}
    <div id="done" class="fixed inset-0 hidden items-center justify-center bg-slate-900/60 p-6">
        <div class="max-w-sm rounded-3xl bg-white p-8 text-center shadow-xl">
            <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-emerald-100 text-2xl text-emerald-600">✓</div>
            <h2 class="mt-4 text-xl font-bold text-slate-900">Thanh toán thành công!</h2>
            <p class="mt-2 text-sm text-slate-500">Mật khẩu đăng nhập đã được gửi tới email của bạn.</p>
            <a href="{{ route('login') }}" class="mt-6 inline-block rounded-xl bg-brand-600 px-6 py-3 text-sm font-semibold text-white hover:bg-brand-700">Đăng nhập ngay</a>
        </div>
    </div>

    <script>
        // Vẽ QR từ chuỗi VietQR do PayOS trả về.
        (function () {
            var s = @json($qr);
            if (s && window.QRCode) {
                new QRCode(document.getElementById('qr'), { text: s, width: 220, height: 220, correctLevel: QRCode.CorrectLevel.M });
            }
        })();

        // Sao chép.
        document.querySelectorAll('.copy').forEach(function (b) {
            b.addEventListener('click', function () {
                navigator.clipboard.writeText(b.dataset.t).then(function () {
                    var old = b.textContent; b.textContent = 'Đã chép'; setTimeout(function () { b.textContent = old; }, 1200);
                });
            });
        });

        // Poll trạng thái → tự hiện thành công khi đã thanh toán.
        (function () {
            var url = @json($statusUrl);
            var timer = setInterval(function () {
                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (d) {
                        if (d && d.paid) {
                            clearInterval(timer);
                            document.getElementById('done').classList.remove('hidden');
                            document.getElementById('done').classList.add('flex');
                        }
                    }).catch(function () {});
            }, 4000);
        })();
    </script>
</body>
</html>
