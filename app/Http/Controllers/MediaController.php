<?php

namespace App\Http\Controllers;

use App\Models\Content\Question;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Phục vụ audio câu hỏi qua ứng dụng (không để lộ đường dẫn public).
 * File nằm trên storage của v1 — production symlink `storage/app/public` sang v1.
 * Local dev chưa có file → trả 404 (đã thống nhất bỏ qua audio ở local).
 */
class MediaController extends Controller
{
    public function questionAudio(Question $question, ?int $index = null): StreamedResponse
    {
        $path = $index === null
            ? $question->audio_path
            : ($question->metadata['audio_files'][$index] ?? null);

        abort_if(blank($path), 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($path), 404);

        return $disk->response($path, null, [
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
