<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Chấm AI Writing qua OpenAI Chat Completions (JSON mode).
 * Không cấu hình key → trả về mock để dev/flow vẫn chạy.
 */
class AiService
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }

    /**
     * @return array{feedback: array, usage: array}
     */
    public function gradeWriting(array $data, ?string $targetLevel = 'B2'): array
    {
        $part = (int) $data['part'];
        $metadata = $data['metadata'] ?? [];
        $question = $data['question_stem'] ?? '';

        $studentText = is_array($data['student_answer'])
            ? json_encode($data['student_answer'], JSON_UNESCAPED_UNICODE)
            : (string) $data['student_answer'];
        if (mb_strlen($studentText) > 4000) {
            $studentText = mb_substr($studentText, 0, 4000) . '... [truncated]';
        }

        $systemPrompt = view('prompts.writing_system', compact('part', 'targetLevel'))->render();
        $userPrompt = view('prompts.writing_user', compact('part', 'question', 'metadata', 'studentText', 'targetLevel'))->render();

        if (! $this->isConfigured()) {
            Log::info('AiService: chưa có OPENAI_API_KEY → trả mock.');

            return $this->getMockResponse($part);
        }

        $response = Http::withToken($this->apiKey)
            ->timeout(45)
            ->retry(2, 2000, fn ($e) => $e->getCode() === 429)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.3,
                'max_tokens' => 2500,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI API Error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('AI Service failed to provide feedback.');
        }

        $result = $response->json();
        $content = $result['choices'][0]['message']['content'] ?? '{}';
        $feedback = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('AiService JSON Decode Error', ['snippet' => mb_substr($content, -200)]);
            throw new \RuntimeException('AI returned invalid JSON.');
        }

        return [
            'feedback' => $feedback,
            'usage' => [
                'input_tokens' => $result['usage']['prompt_tokens'] ?? null,
                'output_tokens' => $result['usage']['completion_tokens'] ?? null,
                'total_tokens' => $result['usage']['total_tokens'] ?? null,
                'model' => $this->model,
            ],
        ];
    }

    // ── SPEAKING ─────────────────────────────────────────────────────────
    /** Audio (trên disk public) → text bằng Whisper. */
    public function transcribe(string $audioPath): string
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Chưa cấu hình OPENAI_API_KEY.');
        }

        $disk = Storage::disk('public');
        abort_unless($disk->exists($audioPath), 404, "Không tìm thấy audio: {$audioPath}");

        $contents = $disk->get($audioPath);
        $model = config('services.openai.transcribe_model', 'whisper-1');

        $response = Http::withToken($this->apiKey)
            ->timeout(120)
            ->attach('file', $contents, basename($audioPath))
            ->post('https://api.openai.com/v1/audio/transcriptions', ['model' => $model]);

        if ($response->failed()) {
            Log::error('Whisper error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('Phiên âm thất bại.');
        }

        return trim((string) $response->json('text'));
    }

    /** @return array{feedback: array, usage: array} */
    public function gradeSpeaking(array $data, ?string $targetLevel = 'B2'): array
    {
        $part = (int) ($data['part'] ?? 1);
        $question = (string) ($data['question_stem'] ?? '');
        $metadata = $data['metadata'] ?? [];
        $transcript = trim((string) ($data['transcript'] ?? ''));

        if ($transcript === '') {
            throw new \RuntimeException('Transcript rỗng, không có gì để chấm.');
        }
        if (mb_strlen($transcript) > 4000) {
            $transcript = mb_substr($transcript, 0, 4000) . '... [cắt bớt]';
        }

        $systemPrompt = view('prompts.speaking_system', compact('part', 'targetLevel'))->render();
        $userPrompt = view('prompts.speaking_user', compact('part', 'question', 'metadata', 'transcript'))->render();

        if (! $this->isConfigured()) {
            return ['feedback' => ['scores' => [], 'overall_score_10' => 0, 'cefr_level' => 'A2', 'feedback' => [], 'improved_sample' => '', 'suggestions' => []], 'usage' => ['model' => 'mock-mode']];
        }

        $response = Http::withToken($this->apiKey)
            ->timeout(45)
            ->retry(2, 2000, fn ($e) => $e->getCode() === 429)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.3,
                'max_tokens' => 2000,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI speaking error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('AI Service failed to grade speaking.');
        }

        $result = $response->json();
        $feedback = json_decode($result['choices'][0]['message']['content'] ?? '{}', true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('AI returned invalid JSON.');
        }

        return [
            'feedback' => $feedback,
            'usage' => [
                'total_tokens' => $result['usage']['total_tokens'] ?? null,
                'model' => $this->model,
            ],
        ];
    }

    protected function getMockResponse(int $part = 1): array
    {
        $mock = [
            'schema_version' => 3,
            'part' => $part,
            'scores' => ['grammar' => 3, 'vocabulary' => 4, 'coherence' => 3, 'task_fulfillment' => 4],
            'overall_score' => 14,
            'feedback' => [
                'grammar' => 'Một vài lỗi thì nhỏ.',
                'vocabulary' => 'Vốn từ khá tốt.',
                'coherence' => 'Mạch ý hợp lý.',
                'task_fulfillment' => 'Đã đáp ứng yêu cầu đề bài.',
            ],
            'part_responses' => [[
                'input_index' => 0,
                'label' => 'Câu trả lời',
                'improved_sample' => 'This is a better sample answer.',
                'detailed_corrections' => [],
            ]],
            'key_mistakes' => ['Chưa nhất quán thì động từ'],
            'suggestions' => ['Ôn lại thì quá khứ'],
        ];

        return ['feedback' => $mock, 'usage' => ['total_tokens' => 350, 'model' => 'mock-mode']];
    }
}
