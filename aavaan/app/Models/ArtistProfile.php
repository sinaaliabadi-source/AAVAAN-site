<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtistProfile extends Model
{
    protected $fillable = [
        'user_id', 'username', 'field', 'city', 'birth_year', 'years_experience',
        'bio', 'avatar', 'phone_contact', 'email_contact', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function portfolioImages(): HasMany
    {
        return $this->hasMany(PortfolioImage::class)->orderBy('order');
    }

    public function portfolioVideos(): HasMany
    {
        return $this->hasMany(PortfolioVideo::class)->orderBy('order');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(ProductionAccessLog::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('uploads/' . $this->avatar)
            : asset('images/default-avatar.png');
    }

    public function mainReel(): ?PortfolioVideo
    {
        return $this->portfolioVideos()->where('is_reel', true)->first()
            ?? $this->portfolioVideos()->first();
    }
}
