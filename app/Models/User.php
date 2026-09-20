<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const SOURCE_MANUAL = 'manual';
    public const SOURCE_PURCHASE = 'purchase';
    public const SOURCE_PROMO = 'promo';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'source',
        'status',
        'expires_at',
        'target_level',
        'max_devices',
        'violation_count',
        'last_violation_at',
        'devtools_guard_disabled',
        'must_change_password',
        'ai_reset_version',
        'speaking_ai_reset_version',
        'ai_extra_uses',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'expires_at' => 'datetime',
            'last_violation_at' => 'datetime',
            'password' => 'hashed',
            'devtools_guard_disabled' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    // ── Quyền & trạng thái ──────────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /** Còn hạn sử dụng khóa? (null = không giới hạn) */
    public function hasActiveAccess(): bool
    {
        return $this->expires_at === null || $this->expires_at->isFuture();
    }

    /** Đã hết hạn sử dụng? */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    // ── Quan hệ (đều nằm trong db2) ─────────────────────────────────────
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }

    public function mockTests(): HasMany
    {
        return $this->hasMany(MockTest::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function loginSessions(): HasMany
    {
        return $this->hasMany(LoginSession::class);
    }

    public function writingAiUsages(): HasMany
    {
        return $this->hasMany(WritingAiUsage::class);
    }

    // ── Lượt chấm AI Writing ────────────────────────────────────────────
    /** Số lượt chấm AI Writing còn lại ('unlimited' cho admin). */
    public function getRemainingWritingAiCredits(): int|string
    {
        if ($this->isAdmin()) {
            return 'unlimited';
        }

        $used = $this->writingAiUsages()
            ->where('reset_version', $this->ai_reset_version ?? 1)
            ->sum('usage_count');

        $limit = config('services.openai.writing_limit', 10) + ($this->ai_extra_uses ?? 0);

        return max(0, $limit - (int) $used);
    }

    /** Ghi nhận đã dùng 1 lượt chấm AI cho part writing. */
    public function recordWritingAiUsage(int $part): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $this->writingAiUsages()->firstOrCreate([
            'writing_part' => $part,
            'reset_version' => $this->ai_reset_version ?? 1,
        ])->increment('usage_count');
    }
}
