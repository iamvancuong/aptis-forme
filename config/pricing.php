<?php

/*
|--------------------------------------------------------------------------
| Bảng giá APTIS V2 — nguồn sự thật duy nhất
|--------------------------------------------------------------------------
| Trang bán, flow đăng ký và bảng đơn đều đọc từ đây. Sửa giá 1 chỗ.
| ⚠️ GIÁ FAKE TẠM — chốt số thật sau. `original_price` để hiển thị gạch ngang
| (định vị "giảm giá" của v2). Thành tiền = price × quantity.
*/

return [
    'packages' => [
        'week' => [
            'label'          => 'Gói 2 Tuần',
            'unit'           => 'gói',
            'price'          => (int) env('PRICE_WEEK', 199000),          // FAKE
            'original_price' => (int) env('PRICE_WEEK_ORIGINAL', 399000), // FAKE
            'days'           => 14,
            'min'            => 1,
            'max'            => 26,
            'popular'        => false,
        ],
        'month' => [
            'label'          => 'Gói 1 Tháng',
            'unit'           => 'gói',
            'price'          => (int) env('PRICE_MONTH', 349000),          // FAKE
            'original_price' => (int) env('PRICE_MONTH_ORIGINAL', 699000), // FAKE
            'days'           => 30,
            'min'            => 1,
            'max'            => 12,
            'popular'        => true,
        ],
    ],
];
