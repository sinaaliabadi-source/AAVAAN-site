<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Subscription extends Model
{
    protected $fillable = ['user_id', 'plan', 'status', 'starts_at', 'expires_at'];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at?->isFuture();
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => $this->plan === 'yearly' ? now()->addYear() : now()->addMonth(),
        ]);
    }
}
