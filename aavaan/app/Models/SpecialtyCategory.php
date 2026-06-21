<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class SpecialtyCategory extends Model
{
    protected $fillable = [
        'slug', 'name_fa', 'parent_id', 'sort_order', 'icon', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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

    /**
     * Returns own attribute definitions for root categories, or parent's for leaf categories.
     * Respects already-loaded relationships to avoid N+1 queries.
     */
    public function effectiveAttributeDefinitions(): Collection
    {
        if ($this->parent_id !== null) {
            $parent = $this->relationLoaded('parent') ? $this->parent : $this->parent()->first();
            if (!$parent) return collect();

            if ($parent->relationLoaded('attributeDefinitions')) {
                return $parent->attributeDefinitions;
            }
            return $parent->attributeDefinitions()->orderBy('sort_order')->get();
        }

        if ($this->relationLoaded('attributeDefinitions')) {
            return $this->attributeDefinitions;
        }
        return $this->attributeDefinitions()->orderBy('sort_order')->get();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeLeaves($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
