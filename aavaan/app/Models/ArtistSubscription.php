<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistSubscription extends Model
{
    protected $fillable = [
        'user_id', 'plan', 'amount', 'payment_ref', 'payment_authority',
        'payment_status', 'starts_at', 'expires_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->payment_status === 'paid' && $this->expires_at?->isFuture();
    }
}
