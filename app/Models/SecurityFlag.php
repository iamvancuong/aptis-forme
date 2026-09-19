<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityFlag extends Model
{
    public const TYPE_DEVTOOLS = 'devtools';
    public const TYPE_DEVICE = 'device';

    protected $fillable = ['user_id', 'type', 'ip_address', 'user_agent', 'url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
