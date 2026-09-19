<?php

namespace App\Services;

use App\Models\Content\Question;

/**
 * Chấm câu hỏi objective (reading/listening/grammar) từ đáp án lưu ở db1.
 * Writing/Speaking để AI xử lý (Pha 4). Port từ v1, đọc đáp án qua connection legacy.
 */
class GradingService
{
    /** @return array{score: float, is_correct: bool|null, grading_status: string|null} */
    public function gradeQuestion(Question $question, $userAnswer, string $mode = 'practice'): array
    {
        $skill = $question->skill;

        // Writing & Speaking: chấm bằng AI (Pha 4) — ở đây đánh dấu pending.
        if ($skill === 'writing' || $skill === 'speaking') {
            return [
                'score' => 0,
                'is_correct' => null,
                'grading_status' => 'pending',
            ];
        }

        if ($skill === 'grammar') {
            return $this->gradeGrammarQuestion($question, $userAnswer);
        }

        $calculatedScore = $this->calculateScore($question, $userAnswer);
        $isCorrect = abs($calculatedScore - $question->point) < 0.01;

        return [
            'score' => $calculatedScore,
            'is_correct' => $isCorrect,
            'grading_status' => null,
        ];
    }

    /** @return array{attempt_answers: array, total_earned: float, total_possible: float, percentage: float} */
    public function gradeSet($questions, array $answers, string $mode = 'practice'): array
    {
        $attemptAnswers = [];
        $totalPossible = $questions->sum('point');
        $totalEarned = 0;

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;

            if ($userAnswer !== null && $userAnswer !== '' && $userAnswer !== []) {
                $result = $this->gradeQuestion($question, $userAnswer, $mode);
            } else {
                $subjective = in_array($question->skill, ['writing', 'speaking']);
                $result = [
                    'score' => 0,
                    'is_correct' => $subjective ? null : false,
                    'grading_status' => $subjective ? 'pending' : null,
                ];
            }

            $totalEarned += $result['score'];

            $attemptAnswers[] = [
                'question_id' => $question->id,
                'answer' => $userAnswer ?? null,
                'is_correct' => $result['is_correct'],
                'score' => $result['score'],
                'feedback' => null,
                'grading_status' => $result['grading_status'],
                'ai_metadata' => null,
            ];
        }

        $percentage = ($totalPossible > 0) ? ($totalEarned / $totalPossible) * 100 : 0;

        return [
            'attempt_answers' => $attemptAnswers,
            'total_earned' => $totalEarned,
            'total_possible' => $totalPossible,
            'percentage' => $percentage,
        ];
    }

    // ─── Grammar ─────────────────────────────────────────────────────────
    private function gradeGrammarQuestion(Question $question, $userAnswer): array
    {
        return match ($question->part) {
            1 => $this->gradeGrammarMcq($question, $userAnswer),
            2 => $this->gradeVocabQuestion($question, $userAnswer),
            default => ['score' => 0, 'is_correct' => false, 'grading_status' => null],
        };
    }

    private function gradeGrammarMcq(Question $question, $userAnswer): array
    {
        $correctOption = $question->metadata['correct_option'] ?? null;
        $isCorrect = ($correctOption !== null && (string) $userAnswer === (string) $correctOption);

        return [
            'score' => $isCorrect ? (float) $question->point : 0.0,
            'is_correct' => $isCorrect,
            'grading_status' => null,
        ];
    }

    private function gradeVocabQuestion(Question $question, $userAnswer): array
    {
        $metadata = $question->metadata ?? [];
        $correctAnswers = $metadata['correct_answers'] ?? [];
        $totalItems = count($correctAnswers);

        if ($totalItems === 0 || ! is_array($userAnswer)) {
            return ['score' => 0, 'is_correct' => false, 'grading_status' => null];
        }

        $pointPerItem = $question->point / $totalItems;

        $valueCounts = array_count_values(array_values(array_filter($userAnswer, fn ($v) => $v !== null && $v !== '')));
        $duplicateValues = array_keys(array_filter($valueCounts, fn ($c) => $c > 1));

        $earned = 0;
        foreach ($correctAnswers as $pairId => $correctWord) {
            $chosen = $userAnswer[(string) $pairId] ?? null;

            if ($chosen === null || $chosen === '') {
                continue;
            }
            if (in_array($chosen, $duplicateValues)) {
                continue;
            }
            if ((string) $chosen === (string) $correctWord) {
                $earned += $pointPerItem;
            }
        }

        $earned = round($earned, 2);
        $isCorrect = abs($earned - (float) $question->point) < 0.01;

        return ['score' => $earned, 'is_correct' => $isCorrect, 'grading_status' => null];
    }

    // ─── Reading / Listening ─────────────────────────────────────────────
    private function calculateScore(Question $question, $userAnswer): float
    {
        $metadata = $question->metadata;
        $skill = $question->skill;
        $part = $question->part;
        $maxPoints = $question->point;

        if ($skill === 'reading') {
            switch ($part) {
                case 1:
                case 3:
                case 4:
                    $correctAnswers = $metadata['correct_answers'] ?? [];
                    $totalItems = count($correctAnswers);
                    if ($totalItems === 0 || ! is_array($userAnswer)) {
                        return 0;
                    }
                    $correctCount = 0;
                    foreach ($correctAnswers as $idx => $correct) {
                        if (isset($userAnswer[$idx]) && $userAnswer[$idx] == $correct) {
                            $correctCount++;
                        }
                    }

                    return ($correctCount / $totalItems) * $maxPoints;

                case 2:
                    if (! is_array($userAnswer)) {
                        return 0;
                    }
                    $expected = array_slice($metadata['sentences'] ?? [], 1);
                    if (empty($expected) || count($userAnswer) !== count($expected)) {
                        return 0;
                    }
                    foreach ($expected as $idx => $sentence) {
                        $submitted = is_array($userAnswer[$idx] ?? null)
                            ? ($userAnswer[$idx]['text'] ?? null)
                            : ($userAnswer[$idx] ?? null);
                        if ($submitted === null || trim((string) $submitted) !== trim((string) $sentence)) {
                            return 0;
                        }
                    }

                    return $maxPoints;

                default:
                    return 0;
            }
        }

        if ($skill === 'listening') {
            switch ($part) {
                case 1:
                    $correctAnswer = $metadata['correct_answer'] ?? null;
                    if ($correctAnswer !== null && $userAnswer == $correctAnswer) {
                        return $maxPoints;
                    }

                    return 0;

                case 2:
                case 3:
                case 4:
                    $correctAnswers = $metadata['correct_answers'] ?? [];
                    $totalItems = count($correctAnswers);
                    if ($totalItems === 0 || ! is_array($userAnswer)) {
                        return 0;
                    }
                    $correctCount = 0;
                    foreach ($correctAnswers as $idx => $correct) {
                        if (isset($userAnswer[$idx]) && $userAnswer[$idx] == $correct) {
                            $correctCount++;
                        }
                    }

                    return ($correctCount / $totalItems) * $maxPoints;

                default:
                    return 0;
            }
        }

        return 0;
    }
}
