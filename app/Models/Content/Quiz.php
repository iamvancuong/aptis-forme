<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Nội dung v1 (read-only): 1 quiz = 1 kỹ năng × part (vd reading part 1).
 */
class Quiz extends LegacyModel
{
    protected $table = 'quizzes';

    protected $casts = [
        'is_published' => 'boolean',
        'metadata' => 'array',
    ];

    public function sets(): HasMany
    {
        return $this->hasMany(Set::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
