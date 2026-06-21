<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialtyCategory extends Model
{
    protected $fillable = [
        'slug', 'name_fa', 'parent_id', 'sort_order', 'icon', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SpecialtyCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SpecialtyCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function attributeDefinitions(): HasMany
    {
        return $this->hasMany(SpecialtyAttributeDefinition::class, 'category_id')->orderBy('sort_order');
    }

    public function artistSpecialties(): HasMany
    {
        return $this->hasMany(ArtistSpecialty::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
