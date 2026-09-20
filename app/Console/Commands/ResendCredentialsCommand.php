<?php

namespace App\Console\Commands;

use App\Mail\AccountCredentialsMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Đặt lại mật khẩu ngẫu nhiên + gửi lại email đăng nhập cho 1 học viên.
 * Dùng khi email lúc thanh toán không gửi được (SMTP lỗi) — vì mật khẩu cũ
 * là ngẫu nhiên không lưu lại được.
 */
class ResendCredentialsCommand extends Command
{
    protected $signature = 'app:resend-credentials {email}';

    protected $description = 'Đặt lại mật khẩu + gửi lại email đăng nhập cho học viên';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Không tìm thấy tài khoản: ' . $this->argument('email'));

            return self::FAILURE;
        }

        $password = Str::random(10);
        $user->update([
            'password' => Hash::make($password),
            'must_change_password' => true,
        ]);

        try {
            Mail::to($user->email)->send(new AccountCredentialsMail(
                email: $user->email,
                password: $password,
                isNew: true,
                expiresAt: $user->expires_at,
            ));
            $this->info("Đã đặt lại mật khẩu + gửi mail cho {$user->email}.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Đã đặt lại mật khẩu NHƯNG gửi mail lỗi: ' . $e->getMessage());
            $this->warn("Mật khẩu mới (cấp tay nếu cần): {$password}");

            return self::FAILURE;
        }
    }
}
