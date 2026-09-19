<?php

namespace App\Services;

use App\Models\Content\Question;
use Illuminate\Support\Facades\URL;

/**
 * Lọc đáp án khỏi metadata câu hỏi trước khi gửi xuống trình duyệt.
 * Mọi thứ gửi client đều công khai (page source/DevTools/curl thấy hết), nên
 * đáp án phải bị bỏ Ở ĐÂY (server). Chấm điểm luôn đọc đáp án thẳng từ db1
 * (GradingService), nên bỏ khỏi payload không ảnh hưởng điểm.
 */
class QuestionSanitizer
{
    private const SENSITIVE_KEYS = [
        'correct_answers' => [],
        'correct_answer' => null,
        'correct_option' => null,
        'correct_index' => null,
        'explanation' => null,
        'sample_answer' => null,
    ];

    private const SENSITIVE_PATHS = [
        'task1.sample_answer',
        'task2.sample_answer',
    ];

    public function metadataForClient(Question $question): array
    {
        $metadata = $question->metadata ?? [];

        if (! is_array($metadata)) {
            return [];
        }

        foreach (self::SENSITIVE_KEYS as $key => $blank) {
            if (array_key_exists($key, $metadata)) {
                $metadata[$key] = $blank;
            }
        }

        foreach (self::SENSITIVE_PATHS as $path) {
            [$parent, $child] = explode('.', $path, 2);
            if (isset($metadata[$parent]) && is_array($metadata[$parent])
                && array_key_exists($child, $metadata[$parent])) {
                $metadata[$parent][$child] = null;
            }
        }

        // Reading Part 2: thứ tự câu CHÍNH LÀ đáp án → phải xáo trộn.
        if ($question->skill === 'reading' && (int) $question->part === 2) {
            $metadata = $this->shuffleOrderingSentences($metadata, $question);
        }

        return $metadata;
    }

    public function questionForClient(Question $question): array
    {
        return [
            'id' => $question->id,
            'skill' => $question->skill,
            'part' => $question->part,
            'type' => $question->type,
            'title' => $question->title,
            'stem' => $question->stem,
            'audio_path' => $question->audio_path,
            'audio_url' => $this->audioUrl($question),
            'audio_urls' => $this->audioUrls($question),
            'image_path' => $question->image_path,
            'point' => $question->point,
            'order' => $question->order,
            'metadata' => $this->metadataForClient($question),
        ];
    }

    public function audioUrl(Question $question): ?string
    {
        if (blank($question->audio_path) || blank($question->id)) {
            return null;
        }

        return $this->signedAudioUrl($question->id);
    }

    /** @return array<int, string> */
    public function audioUrls(Question $question): array
    {
        $files = $question->metadata['audio_files'] ?? null;

        if (! is_array($files) || blank($question->id)) {
            return [];
        }

        return array_map(
            fn (int $index) => $this->signedAudioUrl($question->id, $index),
            array_keys(array_values($files))
        );
    }

    private function signedAudioUrl(int $questionId, ?int $index = null): string
    {
        $parameters = ['question' => $questionId];
        if ($index !== null) {
            $parameters['index'] = $index;
        }

        return URL::temporarySignedRoute('media.question-audio', now()->addHours(6), $parameters);
    }

    /** @return array<int, array> */
    public function collectionForClient($questions): array
    {
        return collect($questions)
            ->map(fn (Question $q) => $this->questionForClient($q))
            ->values()
            ->all();
    }

    /** Đáp án 1 câu — chỉ phát hành SAU khi học viên đã trả lời (cho panel feedback). */
    public function answerKeyFor(Question $question): array
    {
        $metadata = $question->metadata ?? [];
        $key = [];

        foreach (['correct_answers', 'correct_answer', 'correct_option', 'explanation'] as $field) {
            if (array_key_exists($field, $metadata)) {
                $key[$field] = $metadata[$field];
            }
        }

        if (filled($question->explanation)) {
            $key['explanation'] = $question->explanation;
        }

        if ($question->skill === 'reading' && (int) $question->part === 2) {
            $key['sentences'] = $metadata['sentences'] ?? [];
        }

        return $key;
    }

    private function shuffleOrderingSentences(array $metadata, Question $question): array
    {
        $sentences = $metadata['sentences'] ?? null;

        if (! is_array($sentences) || count($sentences) <= 2) {
            return $metadata;
        }

        $opener = array_shift($sentences);
        $seed = $question->id . '|' . (auth()->id() ?? 'guest');

        $metadata['sentences'] = array_merge([$opener], $this->stableShuffle($sentences, $seed));

        return $metadata;
    }

    private function stableShuffle(array $items, string $seed): array
    {
        $keyed = [];
        foreach (array_values($items) as $index => $item) {
            $keyed[] = ['sort' => md5($seed . '|' . $index), 'item' => $item];
        }
        usort($keyed, fn ($a, $b) => strcmp($a['sort'], $b['sort']));

        return array_column($keyed, 'item');
    }
}
