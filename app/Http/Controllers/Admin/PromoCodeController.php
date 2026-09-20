<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PromoCodeController extends Controller
{
    public function index()
    {
        // User đã từng thanh toán (để tính "đã gia hạn/chuyển đổi").
        $paidUserIds = Order::where('status', Order::STATUS_PAID)
            ->whereNotNull('user_id')->pluck('user_id')->unique()->flip();

        $codes = PromoCode::withCount('redemptions')
            ->with('redemptions:id,promo_code_id,user_id')
            ->orderByDesc('id')
            ->get()
            ->map(function (PromoCode $c) use ($paidUserIds) {
                $userIds = $c->redemptions->pluck('user_id')->filter();
                $converted = $userIds->filter(fn ($id) => $paidUserIds->has($id))->count();
                $used = (int) $c->redemptions_count;

                return [
                    'id' => $c->id,
                    'code' => $c->code,
                    'free_days' => $c->free_days,
                    'max_redemptions' => $c->max_redemptions,
                    'expires_at' => $c->expires_at?->format('Y-m-d'),
                    'expires_label' => $c->expires_at?->format('d/m/Y'),
                    'is_active' => $c->is_active,
                    'expired' => $c->isExpired(),
                    'note' => $c->note,
                    'used' => $used,
                    'converted' => $converted,
                    'conversion' => $used > 0 ? (int) round($converted / $used * 100) : 0,
                ];
            });

        return Inertia::render('Admin/PromoCodes', [
            'codes' => $codes,
            'totals' => [
                'codes' => $codes->count(),
                'used' => (int) $codes->sum('used'),
                'converted' => (int) $codes->sum('converted'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:60', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('promo_codes', 'code')],
            'free_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'max_redemptions' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'expires_at' => ['nullable', 'date', 'after:today'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $data['code'] = mb_strtoupper($data['code']);
        PromoCode::create($data);

        return back()->with('success', "Đã tạo mã {$data['code']}.");
    }

    public function update(Request $request, PromoCode $promoCode)
    {
        $data = $request->validate([
            'free_days' => ['sometimes', 'integer', 'min:1', 'max:3650'],
            'max_redemptions' => ['nullable', 'integer', 'min:1', 'max:1000000'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $promoCode->update($data);

        return back()->with('success', "Đã cập nhật mã {$promoCode->code}.");
    }
}
