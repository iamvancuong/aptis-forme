<?php

namespace App\Models;

use App\Models\Content\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    /**
     * Nạp các section kèm bộ đề (db1) + câu hỏi, áp giới hạn số câu/part.
     * Câu được chọn TẤT ĐỊNH theo crc32(mockId_questionId) để show/submit/result
     * luôn lấy cùng bộ câu cho cùng lượt thi.
     */
    public function getSectionsWithSets(): Collection
    {
        $sections = $this->sections ?? [];

        $allSetIds = [];
        foreach ($sections as $section) {
            if (isset($section['set_ids'])) {
                $allSetIds = array_merge($allSetIds, $section['set_ids']);
            } elseif (isset($section['set_id'])) {
                $allSetIds[] = $section['set_id'];
            }
        }
        $setIds = collect($allSetIds)->unique()->values();

        $sets = Set::with(['questions' => fn ($q) => $q->orderBy('questions.order'), 'quiz'])
            ->whereIn('id', $setIds)->get()->keyBy('id');

        return collect($sections)->map(function ($section, $index) use ($sets) {
            $virtSet = null;

            if (isset($section['set_ids'])) {
                $combined = collect();
                foreach ($section['set_ids'] as $sid) {
                    if ($s = $sets->get($sid)) {
                        $combined = $combined->concat($s->questions);
                    }
                }
                $firstSet = $sets->get($section['set_ids'][0]);
                if ($firstSet) {
                    $virtSet = clone $firstSet;
                    $virtSet->setRelation('questions', $combined);
                }
            } else {
                $virtSet = $sets->get($section['set_id']);
            }

            $limit = config("aptis.exam_part_counts.{$this->skill}.{$section['part']}");
            if ($virtSet && $limit && $virtSet->questions->count() > $limit) {
                $mockId = $this->id;
                $virtSet->setRelation('questions',
                    $virtSet->questions
                        ->sortBy(fn ($q) => crc32($mockId . '_' . $q->id))
                        ->take($limit)
                        ->values()
                );
            }

            return [
                'index' => $index,
                'part' => $section['part'],
                'set_id' => $section['set_id'] ?? ($section['set_ids'][0] ?? null),
                'set' => $virtSet,
            ];
        });
    }
}
