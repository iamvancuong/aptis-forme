<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Mã khuyến mãi (admin tạo): học free `free_days` ngày, có hạn + bật/tắt + trần lượt.
 */
class PromoCode extends Model
{
    protected $fillable = ['code', 'free_days', 'max_redemptions', 'expires_at', 'is_active', 'note'];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'free_days' => 'integer',
        'max_redemptions' => 'integer',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(Redemption::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isFull(): bool
    {
        return $this->max_redemptions !== null
            && $this->redemptions()->count() >= $this->max_redemptions;
    }

    /** Còn dùng được không? Trả về [ok, lý do lỗi]. */
    public function usability(): array
    {
        if (! $this->is_active) {
            return [false, 'Mã đã ngừng hoạt động.'];
        }
        if ($this->isExpired()) {
            return [false, 'Mã đã hết hạn.'];
        }
        if ($this->isFull()) {
            return [false, 'Mã đã hết lượt sử dụng.'];
        }

        return [true, null];
    }
}
