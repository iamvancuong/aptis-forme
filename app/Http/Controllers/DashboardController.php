<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Content\Quiz;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /** Thứ tự hiển thị kỹ năng. */
    private const SKILL_ORDER = ['reading', 'listening', 'grammar', 'writing', 'speaking'];

    public function index()
    {
        $user = auth()->user();

        // Gom nội dung (db1) theo KỸ NĂNG: mỗi kỹ năng có mấy part, tổng mấy bộ đề.
        $quizzes = Quiz::query()
            ->withCount(['sets as sets_count' => fn ($q) => $q->where('is_public', true)])
            ->where('is_published', true)
            ->get();

        // Bài đã làm xong của học viên (db2) — để tính điểm TB + tiến độ thật.
        $attempts = Attempt::query()
            ->where('user_id', $user->id)
            ->whereNotNull('finished_at')
            ->get(['skill', 'score', 'set_id', 'finished_at', 'duration_seconds']);

        $statsBySkill = $attempts->groupBy('skill')->map(function ($group) {
            $scored = $group->whereNotNull('score');

            return [
                'attempts_count' => $group->count(),
                'avg_score' => $scored->count() ? (int) round($scored->avg('score')) : null,
                'done_sets' => $group->pluck('set_id')->filter()->unique()->count(),
            ];
        });

        $skills = $quizzes
            ->groupBy('skill')
            ->map(function ($group, $skill) use ($statsBySkill) {
                $setsCount = (int) $group->sum('sets_count');
                $st = $statsBySkill[$skill] ?? null;
                $doneSets = $st['done_sets'] ?? 0;

                return [
                    'skill' => $skill,
                    'parts_count' => $group->count(),
                    'sets_count' => $setsCount,
                    'attempts_count' => $st['attempts_count'] ?? 0,
                    'avg_score' => $st['avg_score'] ?? null,
                    'done_sets' => $doneSets,
                    'completion' => $setsCount > 0 ? min(100, (int) round($doneSets / $setsCount * 100)) : 0,
                ];
            })
            ->sortBy(fn ($s) => array_search($s['skill'], self::SKILL_ORDER))
            ->values();

        // Điểm trung bình chung + tổng lượt.
        $scoredAll = $attempts->whereNotNull('score');

        // "Bài tiếp theo": kỹ năng có nội dung mà học viên luyện ít nhất (khuyến khích cân bằng).
        $next = $skills
            ->sortBy(fn ($s) => [$s['attempts_count'], array_search($s['skill'], self::SKILL_ORDER)])
            ->first();

        return Inertia::render('Dashboard', [
            'skills' => $skills,
            'stats' => [
                'name' => $user->name,
                'streak' => $this->currentStreak($attempts),
                'overall' => $scoredAll->count() ? (int) round($scoredAll->avg('score')) : null,
                'total_attempts' => $attempts->count(),
                'study_minutes' => (int) round($attempts->sum('duration_seconds') / 60),
            ],
            'next' => $next ? ['skill' => $next['skill']] : null,
        ]);
    }

    /** Số ngày luyện liên tiếp (tính tới hôm nay hoặc hôm qua). */
    private function currentStreak($attempts): int
    {
        $days = $attempts
            ->pluck('finished_at')
            ->filter()
            ->map(fn (Carbon $d) => $d->toDateString())
            ->unique()
            ->flip();

        if ($days->isEmpty()) {
            return 0;
        }

        $cursor = Carbon::today();

        // Cho phép streak tính cả khi hôm nay chưa luyện nhưng hôm qua có.
        if (! $days->has($cursor->toDateString())) {
            $cursor = $cursor->subDay();
            if (! $days->has($cursor->toDateString())) {
                return 0;
            }
        }

        $streak = 0;
        while ($days->has($cursor->toDateString())) {
            $streak++;
            $cursor = $cursor->subDay();
        }

        return $streak;
    }
}
