<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCode extends Model {
    protected $fillable = ['code','type','value','max_uses','used_count','valid_from','valid_until','is_active','created_by'];
    protected $casts = [
        'is_active'   => 'boolean',
        'value'       => 'decimal:2',
        'valid_from'  => 'datetime',
        'valid_until' => 'datetime',
    ];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function uses(): HasMany { return $this->hasMany(DiscountCodeUse::class); }

    public function isValid(): bool {
        if (!$this->is_active) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        if ($this->valid_from && now()->lt($this->valid_from)) return false;
        if ($this->valid_until && now()->gt($this->valid_until)) return false;
        return true;
    }
}
