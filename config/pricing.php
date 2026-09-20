<?php

/*
|--------------------------------------------------------------------------
| Bảng giá nhaiaptis — nguồn sự thật duy nhất
|--------------------------------------------------------------------------
| Trang bán, flow đăng ký và bảng đơn đều đọc từ đây. Sửa giá 1 chỗ (file này).
| `original_price` = giá gạch ngang (định vị "giảm giá"). Thành tiền = price × quantity.
| Ghi chú: giá để CỐ ĐỊNH trong code (không đọc .env) cho khỏi lệ thuộc cache config.
*/

return [
    'packages' => [
        'week' => [
            'label'          => 'Gói 2 Tuần',
            'unit'           => 'gói',
            'price'          => 150000,   // giá bán
            'original_price' => 499000,   // giá gạch ngang (giảm ~50%)
            'days'           => 14,
            'min'            => 1,
            'max'            => 26,
            'popular'        => false,
        ],
        'month' => [
            'label'          => 'Gói 1 Tháng',
            'unit'           => 'gói',
            'price'          => 250000,   // giá bán
            'original_price' => 799000,   // giá gạch ngang (giảm ~50%)
            'days'           => 30,
            'min'            => 1,
            'max'            => 12,
            'popular'        => true,
        ],
    ],
];
