<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Lượt đổi mã khuyến mãi (free 1 ngày). 1 email = 1 bản ghi (unique).
 */
class Redemption extends Model
{
    protected $fillable = ['email', 'code', 'promo_code_id', 'ip_address', 'fingerprint', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }
}
