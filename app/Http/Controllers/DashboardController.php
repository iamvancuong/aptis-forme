<?php

namespace App\Http\Controllers;

use App\Models\Content\Quiz;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Nội dung đọc từ db1: mỗi quiz = 1 kỹ năng × part, kèm số bộ đề công khai.
        $quizzes = Quiz::query()
            ->withCount(['sets as sets_count' => fn ($q) => $q->where('is_public', true)])
            ->with(['sets' => fn ($q) => $q->where('is_public', true)->orderBy('order')->limit(1)])
            ->where('is_published', true)
            ->orderBy('skill')
            ->orderBy('part')
            ->get();

        $skills = $quizzes->map(fn ($quiz) => [
            'skill' => $quiz->skill,
            'part' => $quiz->part,
            'sets_count' => $quiz->sets_count,
            'first_set_id' => $quiz->sets->first()?->id,
        ])->values();

        return Inertia::render('Dashboard', [
            'skills' => $skills,
        ]);
    }
}
