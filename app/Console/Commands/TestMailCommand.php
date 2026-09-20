<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'app:test-mail {email}';

    protected $description = 'Gửi email test để kiểm tra cấu hình SMTP';

    public function handle(): int
    {
        $email = $this->argument('email');

        try {
            Mail::raw('Email test từ nhaiaptis — nếu bạn nhận được, SMTP đã hoạt động.',
                fn ($m) => $m->to($email)->subject('Test nhaiaptis'));
            $this->info("Đã gửi tới {$email}. Kiểm tra hộp thư (cả Spam/Quảng cáo).");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Lỗi gửi mail: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
