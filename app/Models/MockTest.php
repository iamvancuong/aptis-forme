<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Lượt thi thử của học viên v2 (db2). `sections` = [{part, set_id}] với set_id thuộc db1.
 */
class MockTest extends Model
{
    protected $fillable = [
        'user_id', 'skill', 'sections', 'duration_minutes',
        'started_at', 'finished_at', 'duration_seconds',
        'score', 'section_scores', 'status',
    ];

    protected $casts = [
        'sections' => 'array',
        'section_scores' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
