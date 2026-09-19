<?php

namespace App\Models;

use App\Models\Content\Question;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Câu trả lời của 1 lượt làm bài.
 * `question()` trỏ sang câu hỏi nội dung ở db1 (đọc qua legacy).
 */
class AttemptAnswer extends Model
{
    protected $fillable = [
        'attempt_id', 'question_id', 'answer', 'is_correct',
        'score', 'feedback', 'ai_metadata', 'grading_status',
    ];

    protected $casts = [
        'answer' => 'array',
        'ai_metadata' => 'array',
        'is_correct' => 'boolean',
        'score' => 'decimal:2',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    /** Câu hỏi nội dung (db1) — chỉ đọc. */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
