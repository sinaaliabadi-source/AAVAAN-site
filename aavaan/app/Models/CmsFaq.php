<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsFaq extends Model
{
    protected $fillable = ['question', 'answer', 'category', 'sort_order', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    public const CATEGORIES = [
        'artist'     => 'هنرمندان',
        'production' => 'تیم‌های تولید',
        'payment'    => 'پرداخت و اشتراک',
        'honarbaz'   => 'هنرباز',
        'general'    => 'عمومی',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
