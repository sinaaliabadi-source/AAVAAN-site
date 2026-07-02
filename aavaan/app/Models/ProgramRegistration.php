<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramRegistration extends Model
{
    protected $fillable = [
        'program_id', 'user_id', 'full_name', 'phone', 'email',
        'city', 'province', 'birth_year', 'gender',
        'talent_type', 'talent_description', 'video_url',
        'guardian_name', 'guardian_phone', 'status', 'admin_notes',
    ];

    protected $casts = [
        'birth_year' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ProgramVote::class, 'registration_id');
    }

    /** آرای تأییدشده (تلفن تأیید شده) */
    public function verifiedVotes(): HasMany
    {
        return $this->votes()->where('phone_verified', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /** فقط نام کوچک برای نمایش عمومی */
    public function firstName(): string
    {
        return trim(explode(' ', trim($this->full_name))[0] ?? $this->full_name);
    }
}
