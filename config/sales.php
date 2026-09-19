<?php

/*
|--------------------------------------------------------------------------
| Cộng tác viên / Sale giới thiệu (referral)
|--------------------------------------------------------------------------
| Mỗi sale có 1 MÃ ngắn (M1, M2…) dùng trong link giới thiệu và nội dung CK.
| Link sinh tự động: /dk/M1/thang · /dk/M1/tuan
| Thêm sale = thêm 1 dòng rồi deploy lại. Mã ≤ 6 ký tự (nhét vào description PayOS).
*/

return [
    'reps' => [
        'M1' => ['name' => 'Sale 1', 'active' => true],
        'M2' => ['name' => 'Sale 2', 'active' => true],
    ],
];
