<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Content\Set;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PracticeController extends Controller
{
    /**
     * Trang làm bài: đọc bộ đề + câu hỏi từ db1, ĐÃ LỌC đáp án trước khi gửi client.
     */
    public function show(Set $set)
    {
        abort_unless($set->is_public, 404);

        $set->load(['quiz', 'questions']);

        $questions = $set->questions->map(function ($q) {
            $meta = $q->metadata ?? [];
            // ⚠️ Không bao giờ để lộ đáp án xuống trình duyệt.
            unset($meta['correct_answers'], $meta['answer'], $meta['answers']);

            return [
                'id' => $q->id,
                'type' => $q->type,
                'stem' => $q->stem,
                'point' => $q->point,
                'metadata' => $meta,
                'has_audio' => ! empty($q->audio_path),
            ];
        })->values();

        return Inertia::render('Practice/Show', [
            'set' => [
                'id' => $set->id,
                'title' => $set->title,
                'skill' => $set->quiz->skill,
                'part' => $set->quiz->part,
            ],
            'questions' => $questions,
        ]);
    }

    /**
     * Nộp bài: chấm ở server (đọc đáp án từ db1), lưu attempt + answers vào db2.
     */
    public function store(Request $request, Set $set)
    {
        abort_unless($set->is_public, 404);

        $data = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        $set->load(['quiz', 'questions']);
        $submitted = $data['answers'];

        $totalPoints = 0;
        $earnedPoints = 0;
        $graded = [];

        foreach ($set->questions as $q) {
            $point = (int) $q->point;
            $totalPoints += $point;

            $answer = $submitted[$q->id] ?? null;
            [$isCorrect, $ratio] = $this->grade($q, $answer);
            $score = round($point * $ratio, 2);
            $earnedPoints += $score;

            $graded[] = [
                'question_id' => $q->id,
                'answer' => $answer,
                'is_correct' => $isCorrect,
                'score' => $score,
            ];
        }

        $percent = $totalPoints > 0 ? round($earnedPoints / $totalPoints * 100, 2) : null;

        $attempt = Attempt::create([
            'user_id' => $request->user()->id,
            'skill' => $set->quiz->skill,
            'mode' => 'practice',
            'set_id' => $set->id,
            'started_at' => now(),
            'finished_at' => now(),
            'duration_seconds' => $data['duration_seconds'] ?? null,
            'score' => $percent,
        ]);

        foreach ($graded as $g) {
            AttemptAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $g['question_id'],
                'answer' => $g['answer'],
                'is_correct' => $g['is_correct'],
                'score' => $g['score'],
                'grading_status' => 'auto',
            ]);
        }

        return redirect()->route('dashboard')->with('success',
            "Đã nộp bài \"{$set->title}\" — điểm: {$percent}%. (Attempt #{$attempt->id} lưu vào db2)");
    }

    /**
     * Chấm 1 câu. Trả [is_correct, tỉ lệ điểm 0..1].
     * Pha 1 hỗ trợ fill_in_blanks_mc (Reading Part 1); các type khác lưu chưa chấm.
     */
    private function grade($question, $answer): array
    {
        $meta = $question->metadata ?? [];

        if ($question->type === 'fill_in_blanks_mc') {
            $correct = $meta['correct_answers'] ?? [];
            $picked = is_array($answer) ? $answer : [];
            $total = count($correct);

            if ($total === 0) {
                return [null, 0.0];
            }

            $hits = 0;
            foreach ($correct as $i => $c) {
                if (isset($picked[$i]) && (string) $picked[$i] === (string) $c) {
                    $hits++;
                }
            }

            return [$hits === $total, $hits / $total];
        }

        // Type chưa hỗ trợ ở Pha 1 → lưu câu trả lời, chưa chấm.
        return [null, 0.0];
    }
}
