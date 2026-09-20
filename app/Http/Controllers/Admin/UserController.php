<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attempt;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        // Tập user đã từng trả phí → để biết "đã gia hạn".
        $paidUserIds = Order::where('status', Order::STATUS_PAID)
            ->whereNotNull('user_id')->pluck('user_id')->unique()->flip();

        $users = User::query()
            ->when($q !== '', fn ($query) => $query->where(fn ($w) =>
                $w->where('email', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%")))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'status' => $u->status,
                'source' => $u->source,
                'is_promo' => $u->source === User::SOURCE_PROMO,
                'converted' => $paidUserIds->has($u->id),
                'expires_at' => $u->expires_at?->format('d/m/Y'),
                'is_active_access' => $u->hasActiveAccess(),
            ]);

        return Inertia::render('Admin/Users', [
            'users' => $users,
            'filters' => ['q' => $q],
        ]);
    }

    public function show(User $user)
    {
        $attempts = Attempt::where('user_id', $user->id)
            ->orderByDesc('id')->limit(30)->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'skill' => $a->skill,
                'mode' => $a->mode,
                'score' => $a->score,
                'created_at' => $a->created_at?->format('d/m/Y H:i'),
            ]);

        $orders = Order::where('user_id', $user->id)->orWhere('email', $user->email)
            ->orderByDesc('id')->limit(20)->get()
            ->map(fn ($o) => [
                'order_code' => $o->order_code,
                'package' => $o->package,
                'amount' => $o->amount,
                'status' => $o->status,
                'paid_at' => $o->paid_at?->format('d/m/Y'),
            ]);

        $converted = Order::where(fn ($w) => $w->where('user_id', $user->id)->orWhere('email', $user->email))
            ->where('status', Order::STATUS_PAID)->exists();
        $promoCode = \App\Models\Redemption::where('user_id', $user->id)->value('code');

        return Inertia::render('Admin/UserShow', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'source' => $user->source,
                'is_promo' => $user->source === User::SOURCE_PROMO,
                'promo_code' => $promoCode,
                'converted' => $converted,
                'target_level' => $user->target_level,
                'expires_at' => $user->expires_at?->format('d/m/Y'),
                'is_active_access' => $user->hasActiveAccess(),
                'max_devices' => $user->max_devices,
                'violation_count' => $user->violation_count,
                'ai_extra_uses' => $user->ai_extra_uses,
                'ai_remaining' => $user->getRemainingWritingAiCredits(),
            ],
            'attempts' => $attempts,
            'orders' => $orders,
        ]);
    }

    public function addAi(Request $request, User $user)
    {
        $data = $request->validate(['amount' => 'required|integer|min:1|max:1000']);
        $user->increment('ai_extra_uses', $data['amount']);

        return back()->with('success', "Đã thêm {$data['amount']} lượt AI cho {$user->email}.");
    }

    public function resetAi(User $user)
    {
        $user->update([
            'ai_reset_version' => ($user->ai_reset_version ?? 1) + 1,
            'ai_extra_uses' => 0,
        ]);

        return back()->with('success', "Đã reset lượt AI của {$user->email} về mặc định.");
    }

    public function block(User $user)
    {
        $user->update(['status' => 'blocked']);

        return back()->with('success', "Đã khóa {$user->email}.");
    }

    public function unblock(User $user)
    {
        $user->update(['status' => 'active', 'violation_count' => 0]);

        return back()->with('success', "Đã mở khóa {$user->email}.");
    }

    public function extend(Request $request, User $user)
    {
        $data = $request->validate(['days' => 'required|integer|min:1|max:3650']);

        $base = ($user->expires_at && $user->expires_at->isFuture()) ? $user->expires_at : now();
        $user->update(['expires_at' => $base->copy()->addDays($data['days']), 'status' => 'active']);

        return back()->with('success', "Đã gia hạn {$data['days']} ngày cho {$user->email} (đến {$user->expires_at->format('d/m/Y')}).");
    }
}
