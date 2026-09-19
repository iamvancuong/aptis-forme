<?php

namespace App\Http\Controllers;

use App\Models\Content\Quiz;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /** Thứ tự hiển thị kỹ năng. */
    private const SKILL_ORDER = ['reading', 'listening', 'grammar', 'writing', 'speaking'];

    public function index()
    {
        // Gom nội dung (db1) theo KỸ NĂNG: mỗi kỹ năng có mấy part, tổng mấy bộ đề.
        $quizzes = Quiz::query()
            ->withCount(['sets as sets_count' => fn ($q) => $q->where('is_public', true)])
            ->where('is_published', true)
            ->get();

        $skills = $quizzes
            ->groupBy('skill')
            ->map(fn ($group, $skill) => [
                'skill' => $skill,
                'parts_count' => $group->count(),
                'sets_count' => (int) $group->sum('sets_count'),
            ])
            ->sortBy(fn ($s) => array_search($s['skill'], self::SKILL_ORDER))
            ->values();

        return Inertia::render('Dashboard', ['skills' => $skills]);
    }
}
