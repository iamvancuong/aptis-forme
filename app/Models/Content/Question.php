<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Nội dung v1 (read-only): 1 câu hỏi. `metadata` chứa đáp án/lựa chọn,
 * `audio_path`/`image_path` trỏ tới media trên storage của v1 (dùng chung).
 */
class Question extends LegacyModel
{
    protected $table = 'questions';

    protected $casts = [
        'metadata' => 'array',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function sets(): BelongsToMany
    {
        return $this->belongsToMany(Set::class, 'set_question');
    }
}
