<?php

namespace App\Http\Controllers;

use App\Models\MockTest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $skill = $request->get('skill', 'reading');
        if (! in_array($skill, ['reading', 'listening'])) {
            $skill = 'reading';
        }

        $leaderboard = MockTest::with('user')
            ->where('skill', $skill)
            ->where('status', 'completed')
            ->whereNotNull('score')
            ->orderByDesc('score')
            ->orderBy('duration_seconds')
            ->limit(50)
            ->get()
            ->unique('user_id')
            ->take(20)
            ->values()
            ->map(fn ($m, $i) => [
                'rank' => $i + 1,
                'name' => $m->user?->name ?? 'Ẩn danh',
                'score' => round($m->score),
                'is_me' => $m->user_id === $request->user()->id,
            ]);

        return Inertia::render('Leaderboard/Index', [
            'skill' => $skill,
            'leaderboard' => $leaderboard,
        ]);
    }
}
