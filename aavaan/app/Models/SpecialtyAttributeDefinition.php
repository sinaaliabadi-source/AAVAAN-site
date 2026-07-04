<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialtyAttributeDefinition extends Model
{
    public const FIELD_TYPES = [
        'text', 'textarea', 'number', 'boolean',
        'select', 'multiselect', 'url', 'date', 'file_link',
    ];

    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRODUCTION_ONLY = 'production_team_only';

    protected $fillable = [
        'category_id', 'key', 'label_fa', 'field_type', 'unit',
        'options', 'is_required', 'is_premium', 'visibility', 'is_filterable', 'sort_order',
    ];

    protected $casts = [
        'options'       => 'array',
        'is_required'   => 'boolean',
        'is_premium'    => 'boolean',
        'is_filterable' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SpecialtyCategory::class, 'category_id');
    }

    public function isSelectType(): bool
    {
        return in_array($this->field_type, ['select', 'multiselect'], true);
    }
}
