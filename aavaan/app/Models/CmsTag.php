<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class CmsTag extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(CmsPost::class, 'cms_post_tags', 'tag_id', 'post_id');
    }

    /** یافتن یا ساختن تگ بر اساس نام. */
    public static function findOrCreateByName(string $name): self
    {
        $name = trim($name);
        $slug = Str::slug($name) ?: 'tag-' . Str::random(5);

        return static::firstOrCreate(
            ['slug' => $slug],
            ['name' => $name, 'created_at' => now()],
        );
    }
}
