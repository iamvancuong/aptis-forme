<?php

namespace App\Models\Content;

/**
 * Nội dung v1 (read-only): showcase học viên điểm cao (name/avatar/certificate)
 * do admin v1 curate — dùng cho trang marketing của v2.
 */
class HighScore extends LegacyModel
{
    protected $table = 'high_scores';

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
