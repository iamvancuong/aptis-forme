<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Đơn hàng v2 (db2) — PayOS tài khoản mới của v2.
 */
class Order extends Model
{
    protected $fillable = [
        'order_code', 'email', 'type', 'package', 'quantity', 'amount',
        'status', 'user_id', 'sale_code', 'payos_link_id', 'paid_at', 'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
