<?php

namespace App\Console\Commands;

use App\Models\Content\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Kiểm tra audio: DB (audio_path) có khớp file trên storage (symlink sang v1) không.
 */
class CheckAudioCommand extends Command
{
    protected $signature = 'app:check-audio {question? : ID câu hỏi cụ thể (bỏ trống = lấy 1 câu listening có audio)}';

    protected $description = 'Kiểm tra kết nối audio giữa DB và storage v1';

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $root = config('filesystems.disks.public.root');

        $this->info('Disk "public" root: ' . $root);
        $this->info('Root có tồn tại?  : ' . (is_dir($root) ? 'CÓ' : 'KHÔNG'));
        $this->info('Root là symlink?  : ' . (is_link($root) ? ('CÓ → ' . readlink($root)) : 'KHÔNG (thư mục thật)'));
        $this->line('');

        $q = $this->argument('question')
            ? Question::find($this->argument('question'))
            : Question::whereNotNull('audio_path')->where('skill', 'listening')->first();

        if (! $q) {
            $this->error('Không tìm thấy câu hỏi listening nào có audio_path trong DB.');

            return self::FAILURE;
        }

        $this->info("Câu hỏi #{$q->id} — skill={$q->skill}, part={$q->part}");
        $this->line('audio_path : ' . ($q->audio_path ?: '(trống)'));

        if ($q->audio_path) {
            $exists = $disk->exists($q->audio_path);
            $this->line('→ File tồn tại? : ' . ($exists ? '✅ CÓ' : '❌ KHÔNG'));
            $this->line('→ Đường dẫn đầy đủ: ' . $root . '/' . $q->audio_path);
        }

        $files = $q->metadata['audio_files'] ?? [];
        if (is_array($files) && $files) {
            $this->line('audio_files (' . count($files) . '):');
            foreach ($files as $i => $f) {
                $this->line("  [{$i}] {$f} — " . ($disk->exists($f) ? '✅' : '❌'));
            }
        }

        return self::SUCCESS;
    }
}
