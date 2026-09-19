<?php

/*
|--------------------------------------------------------------------------
| Cấu hình đề thi thử (Mock Test) — v2
|--------------------------------------------------------------------------
| (Đã bỏ phần lớp học online của v1.)
*/

return [
    // Các part xuất hiện trong 1 đề full theo kỹ năng.
    'exam_sections' => [
        'reading' => [1, 2, 3, 4],
        'listening' => [1, 2, 3, 4],
        'writing' => [1, 2, 3, 4],
        'speaking' => [1, 2, 3, 4],
    ],

    // Thời lượng (phút).
    'exam_duration' => [
        'reading' => 35,
        'listening' => 35,
        'writing' => 50,
        'speaking' => 12,
    ],

    // Số câu lấy cho mỗi part (mặc định 1 nếu không khai).
    'exam_part_counts' => [
        'listening' => [
            1 => 13,
            2 => 1,
            3 => 1,
            4 => 2,
        ],
        'reading' => [
            1 => 1,
            2 => 2,
            3 => 1,
            4 => 1,
        ],
    ],
];
