<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class ProductionAccess extends Model
{
    protected $fillable = ['user_id', 'access_type', 'bundle_size', 'used_count', 'expires_at', 'admin_note'];

    protected $casts = [
        'expires_at' => 'datetime',
        'bundle_size' => 'integer',
        'used_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ProductionAccessLog::class);
    }

    public function hasCredits(): bool
    {
        return $this->payment?->isPaid()
            && $this->used_count < $this->bundle_size
            && (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function remainingCredits(): int
    {
        return max(0, $this->bundle_size - $this->used_count);
    }
}
