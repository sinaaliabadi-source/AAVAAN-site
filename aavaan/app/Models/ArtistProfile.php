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
        // اگر هنرمند عکس پروفایل نگذاشته باشد، favicon برندِ آوان به‌عنوان تصویر پیش‌فرض استفاده می‌شود.
        return $this->avatar
            ? asset('uploads/' . $this->avatar)
            : asset('favicon.svg');
    }

    public function mainReel(): ?PortfolioVideo
    {
        return $this->portfolioVideos()->where('is_reel', true)->first()
            ?? $this->portfolioVideos()->first();
    }

    /** درصد تکمیل پروفایل + فهرست موارد ناقص. منبع واحد این منطق. */
    public function completionData(): array
    {
        $items = [
            ['ok' => (bool) $this->field,        'weight' => 15, 'label' => 'رشته هنری'],
            ['ok' => (bool) $this->city,         'weight' => 10, 'label' => 'شهر'],
            ['ok' => (bool) $this->birth_year,   'weight' => 10, 'label' => 'سال تولد'],
            ['ok' => (bool) $this->bio,          'weight' => 15, 'label' => 'بیوگرافی'],
            ['ok' => (bool) $this->avatar,       'weight' => 20, 'label' => 'تصویر پروفایل'],
            ['ok' => (bool) $this->mainReel(),   'weight' => 15, 'label' => 'ویدیوی ریل'],
            ['ok' => (bool) ($this->phone_contact || $this->email_contact),
                                                 'weight' => 15, 'label' => 'اطلاعات تماس'],
        ];

        $percent = array_sum(array_map(fn($i) => $i['ok'] ? $i['weight'] : 0, $items));
        $hints   = array_values(array_map(
            fn($i) => $i['label'],
            array_filter($items, fn($i) => ! $i['ok'])
        ));

        return ['percent' => $percent, 'hints' => $hints];
    }
}
