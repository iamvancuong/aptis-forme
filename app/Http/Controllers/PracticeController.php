<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Content\Set;
use App\Services\GradingService;
use App\Services\QuestionSanitizer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PracticeController extends Controller
{
    public function __construct(
        private GradingService $grading,
        private QuestionSanitizer $sanitizer,
    ) {}

    /** Trang làm bài: đọc set + câu hỏi từ db1, ĐÃ LỌC đáp án trước khi gửi client. */
    public function show(Set $set)
    {
        abort_unless($set->is_public, 404);

        $set->load(['quiz', 'questions']);

        return Inertia::render('Practice/Show', [
            'set' => [
                'id' => $set->id,
                'title' => $set->title,
                'skill' => $set->quiz->skill,
                'part' => $set->quiz->part,
            ],
            'questions' => $this->sanitizer->collectionForClient($set->questions),
        ]);
    }

    /** Nộp bài: chấm ở server (đọc đáp án từ db1), lưu attempt + answers vào db2. */
    public function store(Request $request, Set $set)
    {
        abort_unless($set->is_public, 404);

        $data = $request->validate([
            'answers' => ['nullable', 'array'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        $set->load(['quiz', 'questions']);
        $answers = $data['answers'] ?? [];

        $result = $this->grading->gradeSet($set->questions, $answers, 'practice');

        // Speaking: lưu các file ghi âm (mỗi sub-câu 1 file) → answer = mảng path.
        foreach ($result['attempt_answers'] as &$a) {
            $q = $set->questions->firstWhere('id', $a['question_id']);
            if ($q && $q->skill === 'speaking' && $request->hasFile("answers.{$q->id}")) {
                $files = $request->file("answers.{$q->id}");
                $files = is_array($files) ? $files : [$files];
                $a['answer'] = array_map(fn ($f) => $f->store('speaking_attempts', 'public'), $files);
            }
        }
        unset($a);

        $attempt = Attempt::create([
            'user_id' => $request->user()->id,
            'skill' => $set->quiz->skill,
            'mode' => 'practice',
            'set_id' => $set->id,
            'started_at' => now(),
            'finished_at' => now(),
            'duration_seconds' => $data['duration_seconds'] ?? null,
            'score' => round($result['percentage'], 2),
        ]);

        foreach ($result['attempt_answers'] as $a) {
            AttemptAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $a['question_id'],
                'answer' => $a['answer'],
                'is_correct' => $a['is_correct'],
                'score' => $a['score'],
                'grading_status' => $a['grading_status'] ?? 'auto',
            ]);
        }

        return redirect()->route('history.show', $attempt)->with('success',
            "Đã nộp bài \"{$set->title}\" — điểm: " . round($result['percentage']) . '%.');
    }
}
