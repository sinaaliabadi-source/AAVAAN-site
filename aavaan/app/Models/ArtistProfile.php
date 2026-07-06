<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ArtistProfile extends Model
{
    /**
     * تولید username یکتای URL-امن از روی نام هنرمند.
     * منبع واحد این منطق؛ هم فرم افزودن کاربر ادمین و هم هر جای دیگر باید همین را reuse کند
     * (کپی نشود). نام‌های فارسی با Str::slug به رشتهٔ خالی می‌رسند؛ در آن حالت پیشوند «honarmand»
     * با پسوند تصادفی کوتاه استفاده می‌شود.
     */
    public static function generateUniqueUsername(string $name): string
    {
        $base = Str::slug($name, '-');
        if ($base === '') {
            $base = 'honarmand-' . Str::lower(Str::random(5));
        }

        $username = $base;
        $suffix   = 1;
        while (static::where('username', $username)->exists()) {
            $username = $base . '-' . $suffix;
            $suffix++;
        }

        return $username;
    }

    protected $fillable = [
        'user_id', 'username', 'field', 'city', 'birth_year', 'gender', 'years_experience',
        'bio', 'avatar', 'phone_contact', 'email_contact', 'is_active',
        'has_blue_tick', 'blue_tick_granted_at',
        'rating_avg', 'rating_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_blue_tick' => 'boolean',
        'blue_tick_granted_at' => 'datetime',
        'birth_year' => 'integer',
        'years_experience' => 'integer',
        'profile_views' => 'integer',
        'rating_avg' => 'float',
        'rating_count' => 'integer',
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

    public function reviews(): HasMany
    {
        return $this->hasMany(ArtistReview::class, 'artist_user_id', 'user_id');
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
