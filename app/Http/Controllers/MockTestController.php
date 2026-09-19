<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Content\Set;
use App\Models\MockTest;
use App\Services\GradingService;
use App\Services\QuestionSanitizer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MockTestController extends Controller
{
    public function __construct(
        private GradingService $grading,
        private QuestionSanitizer $sanitizer,
    ) {}

    /** Kỹ năng có thi thử ở bản này (writing/speaking chờ Pha 4 AI). */
    private const SUPPORTED = ['reading', 'listening'];

    /** Sảnh chờ — thông tin đề. */
    public function create(string $skill)
    {
        abort_unless(in_array($skill, ['reading', 'listening', 'writing', 'speaking']), 404);

        $sections = config("aptis.exam_sections.{$skill}");
        $duration = config("aptis.exam_duration.{$skill}");

        $partInfo = [];
        foreach ($sections as $part) {
            $available = Set::whereHas('quiz', fn ($q) => $q->where('skill', $skill)->where('part', $part))
                ->where('is_public', true)->count();
            $partInfo[] = ['part' => $part, 'available' => $available, 'enough' => $available >= 1];
        }

        $supported = in_array($skill, self::SUPPORTED);
        $canStart = $supported && collect($partInfo)->every(fn ($p) => $p['enough']);

        return Inertia::render('MockTest/Lobby', [
            'skill' => $skill,
            'duration' => $duration,
            'parts' => $partInfo,
            'supported' => $supported,
            'canStart' => $canStart,
        ]);
    }

    /** Bắt đầu — bốc ngẫu nhiên bộ đề cho từng part, tạo lượt thi. */
    public function start(Request $request)
    {
        $data = $request->validate(['skill' => 'required|in:reading,listening']);
        $skill = $data['skill'];

        $sectionConfig = config("aptis.exam_sections.{$skill}");
        $duration = config("aptis.exam_duration.{$skill}");

        $sections = [];
        $usedSetIds = [];

        foreach ($sectionConfig as $part) {
            $setCount = ($skill === 'reading' && $part == 2) ? 2 : 1;

            $sets = Set::whereHas('quiz', fn ($q) => $q->where('skill', $skill)->where('part', $part))
                ->where('is_public', true)
                ->whereNotIn('id', $usedSetIds)
                ->inRandomOrder()
                ->limit($setCount)
                ->get();

            if ($sets->isEmpty()) {
                return back()->with('error', "Không đủ bộ đề cho Part {$part}.");
            }

            $usedSetIds = array_merge($usedSetIds, $sets->pluck('id')->toArray());

            $sections[] = $setCount > 1
                ? ['part' => $part, 'set_ids' => $sets->pluck('id')->toArray()]
                : ['part' => $part, 'set_id' => $sets->first()->id];
        }

        $mockTest = MockTest::create([
            'user_id' => $request->user()->id,
            'skill' => $skill,
            'sections' => $sections,
            'duration_minutes' => $duration,
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('mock-test.show', $mockTest);
    }

    /** Trang thi — render toàn bộ section (đã lọc đáp án) + đồng hồ. */
    public function show(Request $request, MockTest $mockTest)
    {
        abort_unless($mockTest->user_id === $request->user()->id, 403);

        if ($mockTest->status === 'completed') {
            return redirect()->route('mock-test.result', $mockTest);
        }

        $sectionsJson = $mockTest->getSectionsWithSets()->map(fn ($s) => [
            'index' => $s['index'],
            'part' => $s['part'],
            'questions' => $this->sanitizer->collectionForClient($s['set']?->questions ?? collect()),
        ])->values();

        return Inertia::render('MockTest/Exam', [
            'mockTest' => [
                'id' => $mockTest->id,
                'skill' => $mockTest->skill,
                'duration_minutes' => $mockTest->duration_minutes,
                'started_at' => $mockTest->started_at->toIso8601String(),
            ],
            'sections' => $sectionsJson,
        ]);
    }

    /** Nộp toàn bộ — chấm từng section, lưu MockTest + 1 Attempt tổng vào db2. */
    public function submit(Request $request, MockTest $mockTest)
    {
        abort_unless($mockTest->user_id === $request->user()->id, 403);

        if ($mockTest->status === 'completed') {
            return redirect()->route('mock-test.result', $mockTest);
        }

        $data = $request->validate(['answers' => 'required|array']);

        $sectionsWithSets = $mockTest->getSectionsWithSets();
        $sectionScores = [];
        $totalEarned = 0;
        $totalPossible = 0;
        $allAnswers = [];
        $firstSetId = null;

        foreach ($sectionsWithSets as $i => $section) {
            $set = $section['set'];
            if (! $set) {
                continue;
            }
            $firstSetId ??= $set->id;

            $sectionAnswers = $data['answers'][$i] ?? [];
            $result = $this->grading->gradeSet($set->questions, $sectionAnswers, 'mock_test');

            $totalEarned += $result['total_earned'];
            $totalPossible += $result['total_possible'];
            $sectionScores[] = round($result['percentage'], 1);
            $allAnswers = array_merge($allAnswers, $result['attempt_answers']);
        }

        $percent = $totalPossible > 0 ? round($totalEarned / $totalPossible * 100, 2) : 0;
        $finishedAt = now();

        $attempt = Attempt::create([
            'user_id' => $request->user()->id,
            'skill' => $mockTest->skill,
            'mode' => 'mock',
            'set_id' => $firstSetId,
            'mock_test_id' => $mockTest->id,
            'started_at' => $mockTest->started_at,
            'finished_at' => $finishedAt,
            'duration_seconds' => $mockTest->started_at->diffInSeconds($finishedAt),
            'score' => $percent,
        ]);

        foreach ($allAnswers as $a) {
            AttemptAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $a['question_id'],
                'answer' => $a['answer'],
                'is_correct' => $a['is_correct'],
                'score' => $a['score'],
                'grading_status' => $a['grading_status'] ?? 'auto',
            ]);
        }

        $mockTest->update([
            'finished_at' => $finishedAt,
            'duration_seconds' => $mockTest->started_at->diffInSeconds($finishedAt),
            'score' => $percent,
            'section_scores' => $sectionScores,
            'status' => 'completed',
        ]);

        return redirect()->route('mock-test.result', $mockTest);
    }

    /** Kết quả — điểm tổng + từng part. */
    public function result(Request $request, MockTest $mockTest)
    {
        abort_unless($mockTest->user_id === $request->user()->id, 403);

        return Inertia::render('MockTest/Result', [
            'mockTest' => [
                'id' => $mockTest->id,
                'skill' => $mockTest->skill,
                'score' => $mockTest->score,
                'section_scores' => $mockTest->section_scores,
                'duration_seconds' => $mockTest->duration_seconds,
                'status' => $mockTest->status,
            ],
        ]);
    }
}
