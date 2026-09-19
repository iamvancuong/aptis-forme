<?php

namespace App\Http\Controllers;

use App\Models\Content\Set;
use App\Services\GradingService;
use App\Services\QuestionSanitizer;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Học thử KHÔNG cần đăng nhập. Mỗi kỹ năng được làm 1 lần (theo session),
 * vượt quá → chuyển sang đăng ký/mua tài khoản.
 */
class TrialController extends Controller
{
    public function __construct(
        private GradingService $grading,
        private QuestionSanitizer $sanitizer,
    ) {}

    private const SKILLS = ['reading', 'listening', 'grammar', 'writing', 'speaking'];
    private const LABELS = [
        'reading' => 'Reading', 'listening' => 'Listening', 'grammar' => 'Grammar',
        'writing' => 'Writing', 'speaking' => 'Speaking',
    ];

    public function show(Request $request, string $skill)
    {
        abort_unless(in_array($skill, self::SKILLS), 404);

        // Đã đăng nhập thì học thẳng, không tính lượt thử.
        if ($request->user()) {
            return redirect()->route('skills.show', $skill);
        }

        $used = $request->session()->get('trial_used', []);
        if (in_array($skill, $used)) {
            return redirect()->route('register')->with('error',
                'Bạn đã dùng lượt học thử ' . self::LABELS[$skill] . '. Đăng ký để học không giới hạn và lưu kết quả.');
        }

        $set = $this->pickSet($skill);
        if (! $set) {
            return redirect()->route('home')->with('error', 'Chưa có bài học thử cho kỹ năng này.');
        }

        // Đánh dấu đã dùng lượt thử kỹ năng này.
        $request->session()->put('trial_used', array_values(array_unique(array_merge($used, [$skill]))));

        $set->load(['quiz', 'questions']);

        return Inertia::render('Practice/Show', [
            'set' => [
                'id' => $set->id,
                'title' => $set->title,
                'skill' => $set->quiz->skill,
                'part' => $set->quiz->part,
            ],
            'questions' => $this->sanitizer->collectionForClient($set->questions),
            'trial' => true,
        ]);
    }

    /** Kiểm tra tức thời 1 câu (công khai cho học thử). */
    public function check(Request $request, Set $set)
    {
        abort_unless($set->is_public, 404);

        $data = $request->validate([
            'question_id' => ['required', 'integer'],
            'answer' => ['nullable'],
        ]);

        $set->load('questions');
        $q = $set->questions->firstWhere('id', $data['question_id']);
        abort_if(! $q, 404);

        if (in_array($q->skill, ['writing', 'speaking'])) {
            return response()->json(['gradable' => false]);
        }

        $res = $this->grading->gradeQuestion($q, $data['answer']);

        return response()->json([
            'gradable' => true,
            'is_correct' => $res['is_correct'],
            'score' => $res['score'],
            'answer_key' => $this->sanitizer->answerKeyFor($q),
        ]);
    }

    /** Chọn bộ đề cho học thử: ưu tiên Part 1, có câu hỏi. */
    private function pickSet(string $skill): ?Set
    {
        $pick = fn ($query) => $query->where('is_public', true)
            ->withCount('questions')->orderBy('order')->get()
            ->first(fn ($s) => $s->questions_count > 0);

        return $pick(Set::whereHas('quiz', fn ($q) => $q->where('skill', $skill)->where('part', 1)))
            ?? $pick(Set::whereHas('quiz', fn ($q) => $q->where('skill', $skill)));
    }
}
