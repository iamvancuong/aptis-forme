<?php

namespace App\Http\Controllers;

use App\Mail\AccountCredentialsMail;
use App\Models\Redemption;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Nhập mã khuyến mãi → tạo tài khoản học FREE 1 ngày (gửi mật khẩu qua email).
 * Chống lạm dụng: 1 email 1 lần (cứng) + giới hạn mềm theo IP & fingerprint.
 */
class PromoController extends Controller
{
    public function show()
    {
        return view('pages.nhap-ma', [
            'freeDays' => (int) config('promo.free_days'),
            'enabled' => (bool) config('promo.enabled'),
        ]);
    }

    public function redeem(Request $request)
    {
        if (! config('promo.enabled')) {
            return back()->with('error', 'Chương trình học thử miễn phí tạm đóng. Vui lòng quay lại sau.');
        }

        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'code' => ['required', 'string', 'max:60'],
            'fp' => ['nullable', 'string', 'max:255'],
        ]);

        $email = mb_strtolower(trim($data['email']));

        // 1) Mã đúng?
        if (! hash_equals(mb_strtolower((string) config('promo.code')), mb_strtolower(trim($data['code'])))) {
            return back()->withInput()->with('error', 'Mã khuyến mãi không đúng. Vui lòng kiểm tra lại.');
        }

        // 2) Email đã dùng? (đã đổi mã HOẶC đã có tài khoản)
        if (Redemption::where('email', $email)->exists() || User::where('email', $email)->exists()) {
            return back()->withInput()->with('error', 'Email này đã được sử dụng. Mỗi email chỉ nhận ưu đãi 1 lần.');
        }

        // 3) Giới hạn mềm theo IP & thiết bị (chống 1 người tạo nhiều tài khoản)
        $ip = $request->ip();
        $fingerprint = filled($request->input('fp'))
            ? hash('sha256', (string) $request->input('fp'))
            : hash('sha256', (string) $request->userAgent() . '|' . $ip);

        $since = now()->subDay();
        $ipCount = Redemption::where('ip_address', $ip)->where('created_at', '>=', $since)->count();
        $fpCount = Redemption::where('fingerprint', $fingerprint)->where('created_at', '>=', $since)->count();

        if ($ipCount >= (int) config('promo.max_per_ip_day') || $fpCount >= (int) config('promo.max_per_fingerprint_day')) {
            return back()->withInput()->with('error',
                'Hệ thống phát hiện đã có tài khoản miễn phí được tạo từ thiết bị/mạng của bạn. '
                . 'Mỗi người chỉ nhận ưu đãi 1 lần. Vui lòng đăng ký gói để tiếp tục sử dụng.');
        }

        // 4) Tạo tài khoản free + ghi nhận đổi mã (transaction, chống race email trùng)
        $days = (int) config('promo.free_days');
        $password = Str::random(10);

        try {
            $user = DB::transaction(function () use ($email, $data, $ip, $fingerprint, $days, $password) {
                $user = User::create([
                    'name' => strtok($email, '@'),
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => 'user',
                    'source' => User::SOURCE_PROMO,
                    'status' => 'active',
                    'max_devices' => 2,
                    'violation_count' => 0,
                    'must_change_password' => true,
                    'expires_at' => now()->addDays($days),
                ]);

                Redemption::create([
                    'email' => $email,
                    'code' => $data['code'],
                    'ip_address' => $ip,
                    'fingerprint' => $fingerprint,
                    'user_id' => $user->id,
                ]);

                return $user;
            });
        } catch (QueryException $e) {
            // Email trùng do 2 request cùng lúc → coi như đã dùng.
            return back()->withInput()->with('error', 'Email này đã được sử dụng. Mỗi email chỉ nhận ưu đãi 1 lần.');
        }

        Mail::to($user->email)->send(new AccountCredentialsMail(
            email: $user->email,
            password: $password,
            isNew: true,
            expiresAt: $user->expires_at,
        ));

        return back()->with('success',
            "Tạo tài khoản thành công! Mật khẩu đăng nhập đã gửi tới email {$user->email} "
            . "(kiểm tra cả mục Spam/Quảng cáo). Tài khoản dùng free {$days} ngày.");
    }
}
