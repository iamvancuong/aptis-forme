<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Content\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/**
 * Hoạt động làm bài của toàn bộ học viên — để biết thực sự bao nhiêu người đang dùng web.
 */
class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $skill = (string) $request->query('skill', '');
        $days = (int) $request->query('days', 7);
        $q = trim((string) $request->query('q', ''));
        $from = $days > 0 ? now()->subDays($days)->startOfDay() : null;

        $activeUsers = fn ($since) => Attempt::where('created_at', '>=', $since)->distinct()->count('user_id');

        $stats = [
            'active_today' => $activeUsers(now()->startOfDay()),
            'active_7d' => $activeUsers(now()->subDays(7)),
            'active_30d' => $activeUsers(now()->subDays(30)),
            'attempts_today' => Attempt::where('created_at', '>=', now()->startOfDay())->count(),
        ];

        // Theo kỹ năng trong khoảng lọc: số lượt + số người
        $bySkill = Attempt::query()
            ->when($from, fn ($w) => $w->where('created_at', '>=', $from))
            ->select('skill', DB::raw('COUNT(*) as attempts'), DB::raw('COUNT(DISTINCT user_id) as users'))
            ->groupBy('skill')->orderByDesc('attempts')->get();

        // Người dùng tích cực nhất trong khoảng lọc
        $topUsers = Attempt::query()
            ->when($from, fn ($w) => $w->where('attempts.created_at', '>=', $from))
            ->join('users', 'users.id', '=', 'attempts.user_id')
            ->select('users.id', 'users.name', 'users.email',
                DB::raw('COUNT(*) as attempts'), DB::raw('MAX(attempts.created_at) as last_at'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('attempts')->limit(10)->get()
            ->map(fn ($u) => [
                'id' => $u->id, 'name' => $u->name, 'email' => $u->email,
                'attempts' => (int) $u->attempts,
                'last_at' => $u->last_at ? \Illuminate\Support\Carbon::parse($u->last_at)->format('d/m H:i') : null,
            ]);

        $attempts = Attempt::query()
            ->with('user:id,name,email')
            ->withCount('answers')
            ->when($from, fn ($w) => $w->where('created_at', '>=', $from))
            ->when($skill !== '', fn ($w) => $w->where('skill', $skill))
            ->when($q !== '', fn ($w) => $w->whereHas('user', fn ($u) => $u->where(fn ($x) =>
                $x->where('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%"))))
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        // Tên bộ đề đọc từ db1 — nạp 1 lần theo trang.
        $setTitles = Set::whereIn('id', $attempts->getCollection()->pluck('set_id')->filter()->unique())
            ->pluck('title', 'id');

        $attempts->through(fn ($a) => [
            'id' => $a->id,
            'user' => $a->user ? ['id' => $a->user->id, 'name' => $a->user->name, 'email' => $a->user->email] : null,
            'skill' => $a->skill,
            'mode' => $a->mode,
            'set_title' => $setTitles[$a->set_id] ?? null,
            'score' => $a->score,
            'answers_count' => $a->answers_count,
            'duration_seconds' => $a->duration_seconds,
            'created_at' => $a->created_at?->format('d/m/Y H:i'),
        ]);

        return Inertia::render('Admin/Activity', [
            'stats' => $stats,
            'bySkill' => $bySkill,
            'topUsers' => $topUsers,
            'attempts' => $attempts,
            'filters' => ['skill' => $skill, 'days' => $days, 'q' => $q],
        ]);
    }
}
