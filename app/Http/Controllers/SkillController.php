<?php

namespace App\Http\Controllers;

use App\Models\Content\Quiz;
use Inertia\Inertia;

class SkillController extends Controller
{
    private const SKILLS = ['reading', 'listening', 'grammar', 'writing', 'speaking'];

    public function show(string $skill)
    {
        abort_unless(in_array($skill, self::SKILLS), 404);

        // Các part của kỹ năng, mỗi part kèm danh sách bộ đề công khai.
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
            'parts' => $parts,
        ]);
    }
}
