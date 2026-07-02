<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramVote extends Model
{
    protected $fillable = [
        'program_id', 'registration_id', 'voter_ip', 'voter_phone',
        'phone_verified', 'verification_code', 'code_expires_at',
    ];

    protected $casts = [
        'phone_verified'  => 'boolean',
        'code_expires_at' => 'datetime',
    ];

    protected $hidden = [
        'verification_code',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(ProgramRegistration::class, 'registration_id');
    }

    public function scopeVerified($query)
    {
        return $query->where('phone_verified', true);
    }

    public function codeExpired(): bool
    {
        return $this->code_expires_at === null || $this->code_expires_at->isPast();
    }
}
