<?php

namespace App\Http\Middleware;

use App\Models\LoginSession;
use App\Models\SecurityFlag;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Giới hạn số thiết bị dùng ĐỒNG THỜI trên một tài khoản (config/devices.php).
 *   - Tối đa `max_devices` thiết bị hoạt động cùng lúc.
 *   - Vượt trần → +1 vi phạm, đá thiết bị lâu nhất, VẪN cho vào.
 *   - Đủ `block_after_violations` vi phạm → khoá tài khoản.
 *   - Vi phạm cũ hơn `violation_reset_days` ngày thì bỏ qua.
 *
 * Chỉ đếm phiên còn hoạt động trong `activity_window_hours`, và tra theo cặp
 * (device_id, user_id) — không cướp dòng của tài khoản khác.
 */
class SessionLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $next($request);
        }

        /** @var User $user */
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $next($request);
        }

        $deviceId = $request->cookie('aptis_device_id');
        $isNewDevice = false;

        if (! $deviceId) {
            $deviceId = hash('sha256', Str::uuid() . time());
            $isNewDevice = true;
        }

        $existing = LoginSession::where('device_id', $deviceId)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->update([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_active_at' => now(),
            ]);

            return $next($request);
        }

        $since = now()->subHours((int) config('devices.activity_window_hours'));

        $active = LoginSession::where('user_id', $user->id)
            ->where('last_active_at', '>=', $since)
            ->count();

        $limit = $user->max_devices ?: (int) config('devices.max_devices');

        if ($active >= $limit) {
            if ($response = $this->handleViolation($user, $limit)) {
                return $response;
            }

            LoginSession::where('user_id', $user->id)
                ->orderBy('last_active_at')
                ->first()
                ?->delete();
        }

        LoginSession::create([
            'user_id' => $user->id,
            'device_id' => $deviceId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'last_active_at' => now(),
        ]);

        $response = $next($request);

        if ($isNewDevice) {
            $response->headers->setCookie(cookie()->forever('aptis_device_id', $deviceId));
        }

        return $response;
    }

    private function handleViolation(User $user, int $limit): ?Response
    {
        $resetDays = (int) config('devices.violation_reset_days');

        $expired = $user->last_violation_at === null
            || $user->last_violation_at->lt(now()->subDays($resetDays));

        $count = ($expired ? 0 : (int) $user->violation_count) + 1;

        $user->forceFill([
            'violation_count' => $count,
            'last_violation_at' => now(),
        ])->save();

        $threshold = (int) config('devices.block_after_violations');

        SecurityFlag::create([
            'user_id' => $user->id,
            'type' => SecurityFlag::TYPE_DEVICE,
            'ip_address' => request()->ip(),
            'user_agent' => (string) request()->userAgent(),
            'url' => "Vi phạm {$count}/{$threshold} · trần {$limit} thiết bị",
        ]);

        if ($count >= $threshold) {
            $user->update(['status' => 'blocked']);
            LoginSession::where('user_id', $user->id)->delete();
            auth()->logout();

            return redirect()->route('login')->with('error',
                "Tài khoản đã bị khoá do đăng nhập quá {$limit} thiết bị cùng lúc nhiều lần."
                . ' Vui lòng liên hệ giảng viên để được mở lại.');
        }

        $remaining = $threshold - $count;

        session()->flash('warning',
            "Cảnh báo {$count}/{$threshold}: tài khoản vừa đăng nhập trên thiết bị mới trong khi"
            . " đã có {$limit} thiết bị đang dùng. Thiết bị lâu không dùng nhất đã bị đăng xuất."
            . " Vi phạm thêm {$remaining} lần nữa là tài khoản bị khoá.");

        return null;
    }
}
