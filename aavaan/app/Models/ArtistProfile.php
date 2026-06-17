<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtistProfile extends Model
{
    protected $fillable = [
        'user_id', 'username', 'field', 'city', 'birth_year', 'years_experience',
        'bio', 'avatar', 'reel_video', 'reel_is_external',
        'phone_contact', 'email_contact', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'reel_is_external' => 'boolean',
        'birth_year' => 'integer',
        'years_experience' => 'integer',
        'profile_views' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workHistories(): HasMany
    {
        return $this->hasMany(WorkHistory::class)->orderByDesc('year');
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class)->orderBy('order');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(ProductionAccessLog::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('uploads/avatars/' . $this->avatar);
        }
        return asset('images/default-avatar.png');
    }

    public function getReelUrlAttribute(): ?string
    {
        if (!$this->reel_video) return null;
        if ($this->reel_is_external) return $this->reel_video;
        return asset('uploads/reels/' . $this->reel_video);
    }
}
