<?php

namespace App\Models\Content;

/**
 * Nội dung v1 (read-only): bài hướng dẫn (có video_url/video_path).
 */
class Instruction extends LegacyModel
{
    protected $table = 'instructions';

    protected $casts = [
        'is_published' => 'boolean',
    ];
}
