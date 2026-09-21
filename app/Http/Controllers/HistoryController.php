<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\Content\Question;
use App\Services\QuestionSanitizer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function __construct(private QuestionSanitizer $sanitizer) {}

    public function index(Request $request)
    {
        $attempts = Attempt::where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(20)
            ->through(fn ($a) => [
                'id' => $a->id,
                'skill' => $a->skill,
                'mode' => $a->mode,
                'score' => $a->score,
                'created_at' => $a->created_at?->format('d/m/Y H:i'),
            ]);

        return Inertia::render('History/Index', ['attempts' => $attempts]);
    }

    public function show(Request $request, Attempt $attempt)
    {
        // Admin được xem bài của mọi học viên (màn Hoạt động).
        $viewer = $request->user();
        abort_unless($attempt->user_id === $viewer->id || $viewer->isAdmin(), 403);

        $attempt->load('answers');
        $set = $attempt->set; // db1 (có thể null nếu mock)
        $set?->load('quiz');

        // Nạp câu hỏi (db1) theo id để hiển thị đề + phát hành đáp án sau khi làm.
        $questionIds = $attempt->answers->pluck('question_id')->all();
        $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');

        $items = $attempt->answers->map(function ($ans) use ($questions) {
            $q = $questions->get($ans->question_id);

            return [
                'answer_id' => $ans->id,
                'skill' => $q?->skill,
                'question' => $q ? $this->sanitizer->questionForClient($q) : null,
                'answer_key' => $q ? $this->sanitizer->answerKeyFor($q) : [],
                'your_answer' => $ans->answer,
                'is_correct' => $ans->is_correct,
                'score' => $ans->score,
                'grading_status' => $ans->grading_status,
                'ai' => $ans->ai_metadata['feedback'] ?? null,
            ];
        })->values();

        return Inertia::render('History/Show', [
            'attempt' => [
                'id' => $attempt->id,
                'skill' => $attempt->skill,
                'mode' => $attempt->mode,
                'score' => $attempt->score,
                'created_at' => $attempt->created_at?->format('d/m/Y H:i'),
                'set_title' => $set?->title,
                // Chỉ gửi khi admin xem bài của người khác
                'owner' => $attempt->user_id !== $viewer->id
                    ? $attempt->user?->only(['id', 'name', 'email'])
                    : null,
            ],
            'items' => $items,
        ]);
    }
}
