<?php

namespace App\Http\Controllers;

use App\Models\Content\Set;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CatalogController extends Controller
{
    /** Thứ tự hiển thị kỹ năng. */
    private const SKILL_ORDER = ['reading', 'listening', 'grammar', 'writing', 'speaking'];

    /** Ngân hàng đề công khai — danh mục TOÀN BỘ đề (không lộ câu hỏi/đáp án). */
    public function index()
    {
        $catalog = self::catalog();

        $totals = [
            'sets' => (int) $catalog->sum('sets_count'),
            'questions' => (int) $catalog->sum('questions_count'),
            'skills' => $catalog->count(),
        ];

        return view('pages.ngan-hang-de', compact('catalog', 'totals'));
    }

    /** Danh mục đề gom theo kỹ năng (cache 15 phút). */
    public static function catalog(): Collection
    {
        return Cache::remember('public_catalog', now()->addMinutes(15), function () {
            $sets = Set::query()
                ->where('is_public', true)
                ->whereHas('quiz', fn ($q) => $q->where('is_published', true))
                ->with(['quiz'])
                ->withCount('questions')
                ->get();

            return $sets
                ->groupBy(fn ($s) => $s->quiz->skill ?? 'other')
                ->map(fn ($group, $skill) => [
                    'skill' => $skill,
                    'sets_count' => $group->count(),
                    'questions_count' => (int) $group->sum('questions_count'),
                    'sets' => $group
                        ->sortBy(fn ($s) => [(int) ($s->quiz->part ?? 0), $s->id])
                        ->map(fn ($s) => [
                            'title' => $s->title ?: ('Đề #' . $s->id),
                            'part' => $s->quiz->part ?? null,
                            'questions_count' => (int) $s->questions_count,
                        ])
                        ->values(),
                ])
                ->sortBy(fn ($s) => array_search($s['skill'], self::SKILL_ORDER))
                ->values();
        });
    }

    /** Thống kê gọn cho trang home. */
    public static function summary(): array
    {
        $catalog = self::catalog();

        return [
            'sets' => (int) $catalog->sum('sets_count'),
            'questions' => (int) $catalog->sum('questions_count'),
            'skills' => $catalog->count(),
        ];
    }
}
