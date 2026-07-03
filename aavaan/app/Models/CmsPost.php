<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CmsPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'cover_image',
        'author_id', 'category_id', 'status', 'is_featured',
        'published_at', 'reading_time', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured'  => 'boolean',
        'view_count'   => 'integer',
        'reading_time' => 'integer',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsCategory::class, 'category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(CmsTag::class, 'cms_post_tags', 'post_id', 'tag_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /** محاسبهٔ زمان مطالعه (دقیقه) بر اساس تعداد کلمات — سازگار با متن فارسی. */
    public static function calculateReadingTime(string $content): int
    {
        $text = trim(strip_tags($content));
        // str_word_count برای فارسی مناسب نیست؛ با شمارش واحدهای جداشده با فاصله می‌شماریم.
        $wordCount = $text === '' ? 0 : count(preg_split('/\s+/u', $text));

        return max(1, (int) ceil($wordCount / 200));
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }
        return Str::startsWith($this->cover_image, ['http://', 'https://', '/'])
            ? $this->cover_image
            : asset($this->cover_image);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'پیش‌نویس',
            'published' => 'منتشرشده',
            'archived'  => 'بایگانی',
            default     => $this->status,
        };
    }

    protected static function booted(): void
    {
        static::creating(function (CmsPost $post) {
            if (empty($post->slug)) {
                $post->slug = static::uniqueSlug($post->title);
            }
        });
    }

    /** تولید slug یکتا (پشتیبانی از عنوان فارسی). */
    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'post-' . Str::random(6);
        }
        $slug = $base;
        $i = 2;
        while (static::withTrashed()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
