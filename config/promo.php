<?php

/*
|--------------------------------------------------------------------------
| Mã khuyến mãi — học FREE 1 ngày (giai đoạn ra mắt)
|--------------------------------------------------------------------------
| Nhập mã → tạo tài khoản free `free_days` ngày, gửi mật khẩu qua email.
| Chống lạm dụng (MỀM): 1 email 1 lần (cứng, DB) + giới hạn theo IP/thiết bị.
| Vượt ngưỡng → KHÔNG cho tạo thêm (không auto-ban để tránh oan người dùng
| chung IP/4G). Lạm dụng thiết bị sau đăng nhập do SessionLimit xử lý.
*/

return [
    // Mã công khai để rải khi SEO/quảng cáo. Đổi trong .env: PROMO_CODE=...
    'code' => env('PROMO_CODE', 'NHAIAPTIS'),

    'free_days' => (int) env('PROMO_FREE_DAYS', 1),

    // Trần số tài khoản free trong 24h — tín hiệu MỀM.
    'max_per_ip_day' => (int) env('PROMO_MAX_PER_IP', 5),   // nhiều người thật có thể chung IP (4G, trường học)
    'max_per_fingerprint_day' => (int) env('PROMO_MAX_PER_FP', 1), // 1 thiết bị ~ 1 lần/ngày

    'enabled' => filter_var(env('PROMO_ENABLED', true), FILTER_VALIDATE_BOOL),
];
