<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Nội dung v1 (read-only): 1 bộ đề thuộc 1 quiz, gồm nhiều câu hỏi qua pivot.
 */
class Set extends LegacyModel
{
    protected $table = 'sets';

    protected $casts = [
        'is_public' => 'boolean',
        'metadata' => 'array',
        'deadline' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questions(): BelongsToMany
    {
        // Thứ tự câu hỏi nằm ở cột questions.order (pivot set_question không có order).
        return $this->belongsToMany(Question::class, 'set_question')
            ->orderBy('questions.order');
    }
}
