<?php

namespace App\Services;

use App\Mail\AccountCredentialsMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Biến đơn đã thanh toán thành kết quả thật: tạo/gia hạn tài khoản + gửi email.
 * Idempotent — gọi lại trên đơn đã fulfill không làm gì thêm (webhook có thể trùng).
 * v2 chỉ có đơn ĐĂNG KÝ (đã bỏ chấm tay).
 */
class OrderFulfillmentService
{
    /** Mật khẩu mặc định cho tài khoản mới; buộc đổi ở lần đăng nhập đầu. */
    public const DEFAULT_PASSWORD = '12345678';

    public function fulfill(Order $order): void
    {
        $days = $order->durationDays();

        // Đổi trạng thái + cấp/gia hạn nằm trong 1 transaction khoá row đơn (và user)
        // để webhook/reconcile chạy song song không cộng hạn/gửi mail 2 lần.
        $mailData = DB::transaction(function () use ($order, $days) {
            $order = Order::whereKey($order->getKey())->lockForUpdate()->first();

            if (! $order || $order->isPaid()) {
                return null;
            }

            $user = User::where('email', $order->email)->lockForUpdate()->first();

            if ($user) {
                $base = ($user->expires_at && $user->expires_at->isFuture())
                    ? $user->expires_at
                    : now();

                $user->update([
                    'expires_at' => $base->copy()->addDays($days),
                    'status' => 'active',
                ]);

                $result = [$user, false, null];
            } else {
                $user = User::create([
                    'name' => strtok($order->email, '@'),
                    'email' => $order->email,
                    'password' => Hash::make(self::DEFAULT_PASSWORD),
                    'role' => 'user',
                    'source' => User::SOURCE_PURCHASE,
                    'status' => 'active',
                    'max_devices' => 2,
                    'violation_count' => 0,
                    'must_change_password' => true,
                    'expires_at' => now()->addDays($days),
                ]);

                $result = [$user, true, self::DEFAULT_PASSWORD];
            }

            $order->update([
                'status' => Order::STATUS_PAID,
                'paid_at' => now(),
                'user_id' => $user->id,
            ]);

            return $result;
        });

        if ($mailData === null) {
            return;
        }

        [$user, $isNew, $password] = $mailData;

        Mail::to($user->email)->send(new AccountCredentialsMail(
            email: $user->email,
            password: $password,
            isNew: $isNew,
            expiresAt: $user->expires_at,
        ));
    }
}
