<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'cover_image',
        'status', 'starts_at', 'ends_at', 'meta',
    ];

    protected $casts = [
        'meta'      => 'array',
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(ProgramRegistration::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(ProgramVote::class);
    }

    public function approvedRegistrations(): HasMany
    {
        return $this->registrations()->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** آیا ثبت‌نام هنوز باز است؟ */
    public function registrationOpen(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }

        return (bool) ($this->meta['registration_enabled'] ?? true);
    }

    /** آیا رأی‌گیری فعال است؟ */
    public function votingOpen(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return (bool) ($this->meta['voting_enabled'] ?? true);
    }
}
