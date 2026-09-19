<?php

namespace App\Http\Controllers;

use App\Models\Content\Quiz;
use App\Models\Content\Set;
use Inertia\Inertia;

class SkillController extends Controller
{
    private const SKILLS = ['reading', 'listening', 'grammar', 'writing', 'speaking'];

    /** Writing/Speaking: mỗi bộ đề là 1 kịch bản đầy đủ (part 1-4) → hiển thị theo BỘ. */
    private const BY_SET = ['writing', 'speaking'];

    public function show(string $skill)
    {
        abort_unless(in_array($skill, self::SKILLS), 404);

        // Writing/Speaking → danh sách bộ đề (mỗi bộ có câu của cả 4 part).
        if (in_array($skill, self::BY_SET)) {
            $sets = Set::whereHas('quiz', fn ($q) => $q->where('skill', $skill))
                ->where('is_public', true)
                ->withCount('questions')
                ->orderBy('order')
                ->orderBy('id')
                ->get()
                ->filter(fn ($s) => $s->questions_count > 0) // bỏ bộ rỗng (legacy)
                ->values()
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'title' => $s->title,
                    'parts' => $s->questions_count,
                ]);

            return Inertia::render('Skills/Show', [
                'skill' => $skill,
                'bySet' => true,
                'sets' => $sets,
            ]);
        }

        // Reading/Listening/Grammar → theo part.
        $parts = Quiz::with(['sets' => fn ($q) => $q->where('is_public', true)->orderBy('order')])
            ->where('skill', $skill)
            ->where('is_published', true)
            ->orderBy('part')
            ->get()
            ->map(fn ($quiz) => [
                'part' => $quiz->part,
                'title' => $quiz->title,
                'sets' => $quiz->sets->map(fn ($s) => [
                    'id' => $s->id,
                    'title' => $s->title,
                ])->values(),
            ]);

        return Inertia::render('Skills/Show', [
            'skill' => $skill,
            'bySet' => false,
            'parts' => $parts,
        ]);
    }
}
