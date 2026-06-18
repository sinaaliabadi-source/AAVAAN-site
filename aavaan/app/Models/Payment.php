<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'payable_type', 'payable_id',
        'amount', 'gateway', 'authority', 'ref_id', 'status', 'paid_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markPaid(string $refId): void
    {
        $this->update([
            'status' => 'paid',
            'ref_id' => $refId,
            'paid_at' => now(),
        ]);
    }
}
