<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtistSpecialty extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'is_primary', 'years_experience', 'attributes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'attributes' => 'array',
    ];

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

    public function photos(): HasMany
    {
        return $this->hasMany(ArtistSpecialtyMedia::class)->where('type', 'photo')->orderBy('sort_order');
    }

    public function videoLinks(): HasMany
    {
        return $this->hasMany(ArtistSpecialtyMedia::class)->where('type', 'video_link')->orderBy('sort_order');
    }
}
