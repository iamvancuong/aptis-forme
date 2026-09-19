<?php

namespace App\Models\Content;

/**
 * Nội dung v1 (read-only): cảm nhận học viên (testimonials) cho trang bán.
 */
class Feedback extends LegacyModel
{
    protected $table = 'feedback';

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
