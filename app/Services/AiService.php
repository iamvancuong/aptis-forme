<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
