<?php

/*
|--------------------------------------------------------------------------
| Cấu hình PayOS — TÀI KHOẢN MỚI của v2
|--------------------------------------------------------------------------
| Lấy Client ID / API Key / Checksum Key từ tài khoản PayOS MỚI (my.payos.vn)
| rồi dán vào .env (KHÔNG commit):
|   PAYOS_CLIENT_ID= · PAYOS_API_KEY= · PAYOS_CHECKSUM_KEY=
| Khi còn trống → PayosService báo lỗi rõ ràng; dev dùng PAYOS_FAKE=true.
*/

return [
    'client_id'    => env('PAYOS_CLIENT_ID', ''),
    'api_key'      => env('PAYOS_API_KEY', ''),
    'checksum_key' => env('PAYOS_CHECKSUM_KEY', ''),

    // 🧪 Giả lập: hiện nút "giả lập đã thanh toán" để test luồng không mất tiền.
    'fake'         => filter_var(env('PAYOS_FAKE', false), FILTER_VALIDATE_BOOL),

    // ⚠️ CHỈ tắt SSL verify ở dev localhost. Production để true.
    'verify_ssl'   => filter_var(env('PAYOS_VERIFY_SSL', true), FILTER_VALIDATE_BOOL),

    'base_url'          => env('PAYOS_BASE_URL', 'https://api-merchant.payos.vn'),
    'checkout_base_url' => env('PAYOS_CHECKOUT_BASE_URL', 'https://pay.payos.vn/web/'),
];
