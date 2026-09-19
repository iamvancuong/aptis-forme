<?php

namespace App\Http\Controllers;

use App\Models\AttemptAnswer;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function __construct(private AiService $ai) {}

    /** Chấm AI một câu Writing đã nộp. */
    public function gradeWriting(Request $request, AttemptAnswer $answer)
    {
        $user = $request->user();
        $answer->load('attempt');

        abort_unless($answer->attempt->user_id === $user->id || $user->isAdmin(), 403);

        $question = $answer->question; // db1
        abort_unless($question && $question->skill === 'writing', 400, 'Không phải câu Viết.');

        if ($answer->grading_status === 'ai_graded') {
            return back()->with('warning', 'Bài này đã được AI chấm.');
        }

        $remaining = $user->getRemainingWritingAiCredits();
        if ($remaining !== 'unlimited' && $remaining <= 0) {
            return back()->with('error', 'Bạn đã hết lượt chấm AI. Vui lòng liên hệ admin.');
        }

        try {
            $result = $this->ai->gradeWriting([
                'part' => $question->part,
                'question_stem' => $question->stem,
                'metadata' => $question->metadata,
                'student_answer' => $answer->answer,
            ], $user->target_level ?? 'B2');

            // overall_score của Writing theo thang 0-5 → quy về % (0-100).
            $overall = $result['feedback']['overall_score']
                ?? ($result['feedback']['scores']['overall_score'] ?? null);
            $percent = $overall !== null ? round(min($overall, 5) / 5 * 100, 1) : null;

            DB::transaction(function () use ($answer, $result, $percent, $user, $question) {
                $answer->update([
                    'ai_metadata' => $result,
                    'score' => $percent,
                    'grading_status' => 'ai_graded',
                ]);

                $user->recordWritingAiUsage((int) $question->part);
            });

            $this->recomputeAttemptScore($answer->attempt);

            return back()->with('success', 'Đã chấm AI xong!');
        } catch (\Throwable $e) {
            Log::error('AI grade writing lỗi: ' . $e->getMessage());

            return back()->with('error', 'Lỗi kết nối AI. Vui lòng thử lại sau.');
        }
    }

    /** Điểm tổng lượt Writing/Speaking = trung bình các câu ĐÃ chấm AI (thang %). */
    private function recomputeAttemptScore($attempt): void
    {
        $attempt->refresh()->load('answers');
        $graded = $attempt->answers->where('grading_status', 'ai_graded');
        $attempt->update([
            'score' => $graded->count() ? round($graded->avg('score'), 1) : null,
        ]);
    }

    /** Chấm AI một câu Speaking: phiên âm (Whisper) rồi chấm. */
    public function gradeSpeaking(Request $request, AttemptAnswer $answer)
    {
        $user = $request->user();
        $answer->load('attempt');
        abort_unless($answer->attempt->user_id === $user->id || $user->isAdmin(), 403);

        $question = $answer->question;
        abort_unless($question && $question->skill === 'speaking', 400, 'Không phải câu Nói.');

        if ($answer->grading_status === 'ai_graded') {
            return back()->with('warning', 'Bài này đã được AI chấm.');
        }

        $paths = $answer->answer;
        $paths = is_array($paths) ? $paths : (is_string($paths) ? [$paths] : []);
        if (empty($paths)) {
            return back()->with('error', 'Không tìm thấy file ghi âm.');
        }

        try {
            // Phiên âm từng đoạn (mỗi sub-câu) rồi ghép lại.
            $parts = [];
            foreach ($paths as $i => $p) {
                $t = $this->ai->transcribe($p);
                $parts[] = 'Câu ' . ($i + 1) . ': ' . $t;
            }
            $transcript = implode("\n", $parts);

            $result = $this->ai->gradeSpeaking([
                'part' => $question->part,
                'question_stem' => $question->stem,
                'metadata' => $question->metadata,
                'transcript' => $transcript,
            ], $user->target_level ?? 'B2');

            $result['transcript'] = $transcript;

            // overall_score_10 (0-10) → % (0-100).
            $overall = $result['feedback']['overall_score_10'] ?? null;
            $percent = $overall !== null ? round(min($overall, 10) / 10 * 100, 1) : null;

            $answer->update([
                'ai_metadata' => $result,
                'score' => $percent,
                'grading_status' => 'ai_graded',
            ]);

            $this->recomputeAttemptScore($answer->attempt);

            return back()->with('success', 'Đã chấm AI phần Nói xong!');
        } catch (\Throwable $e) {
            Log::error('AI grade speaking lỗi: ' . $e->getMessage());

            return back()->with('error', 'Lỗi chấm AI phần Nói: ' . $e->getMessage());
        }
    }
}
