<?php

namespace App\Models;

use App\Models\Content\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Lượt làm bài của học viên v2 (db2).
 * Quan hệ `set()` trỏ sang nội dung ở db1 (đọc qua connection legacy).
 */
class Attempt extends Model
{
    protected $fillable = [
        'user_id', 'skill', 'mode', 'set_id', 'mock_test_id',
        'started_at', 'finished_at', 'duration_seconds', 'score',
        'metadata', 'is_grading_requested', 'is_seen', 'grading_requested_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'grading_requested_at' => 'datetime',
        'score' => 'decimal:2',
        'is_grading_requested' => 'boolean',
        'is_seen' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function mockTest(): BelongsTo
    {
        return $this->belongsTo(MockTest::class);
    }

    /** Bộ đề nội dung (db1) — chỉ đọc. */
    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class, 'set_id');
    }
}
