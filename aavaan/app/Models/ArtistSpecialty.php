<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ArtistSpecialty extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'is_primary', 'years_experience', 'attributes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'attributes' => 'array',
    ];

    /**
     * حذف درخواست‌های تأیید مرتبط هنگام حذف تخصص.
     * روی MariaDB خودِ FK با cascade این کار را می‌کند؛ این هوک تضمین سازگاری روی همهٔ درایورها
     * (از جمله SQLite که cascadeِ FKهای افزوده‌شده با ALTER را اجرا نمی‌کند) است.
     */
    protected static function booted(): void
    {
        static::deleting(function (self $specialty) {
            Verification::where('artist_specialty_id', $specialty->id)->delete();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SpecialtyCategory::class, 'category_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ArtistSpecialtyMedia::class)->orderBy('sort_order');
    }

    /**
     * ردیف‌های نرمال‌شدهٔ مقادیر ویژگی‌ها (ایندکس جستجو).
     * با حذف تخصص، این ردیف‌ها به‌صورت cascade در سطح دیتابیس پاک می‌شوند.
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ArtistSpecialtyAttributeValue::class);
    }

    /**
     * درخواست‌های تأیید این تخصص (type=specialty). با حذف تخصص، cascade در سطح دیتابیس پاک می‌کند.
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class, 'artist_specialty_id')
            ->where('type', Verification::TYPE_SPECIALTY);
    }

    /**
     * جدیدترین درخواست تأیید این تخصص (برای نمایش وضعیت روی کارت).
     */
    public function latestVerification(): HasOne
    {
        return $this->hasOne(Verification::class, 'artist_specialty_id')
            ->where('type', Verification::TYPE_SPECIALTY)
            ->latestOfMany();
    }

    public function isVerified(): bool
    {
        return ($this->relationLoaded('latestVerification') ? $this->latestVerification : $this->latestVerification()->first())
            ?->status === Verification::STATUS_APPROVED;
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ArtistSpecialtyMedia::class)->where('type', 'photo')->orderBy('sort_order');
    }

    public function videoLinks(): HasMany
    {
        return $this->hasMany(ArtistSpecialtyMedia::class)->where('type', 'video_link')->orderBy('sort_order');
    }
}
