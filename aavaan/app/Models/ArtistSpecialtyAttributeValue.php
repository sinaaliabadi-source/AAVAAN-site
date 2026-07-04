<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ردیف نرمال‌شدهٔ یک مقدار ویژگی برای فیلتر SQL.
 * منبع حقیقت ستون JSON «attributes» روی ArtistSpecialty است؛ این مدل صرفاً ایندکس جستجوست.
 */
class ArtistSpecialtyAttributeValue extends Model
{
    protected $fillable = [
        'artist_specialty_id', 'definition_id', 'value_string', 'value_number',
    ];

    protected $casts = [
        'value_number' => 'decimal:2',
    ];

    public function artistSpecialty(): BelongsTo
    {
        return $this->belongsTo(ArtistSpecialty::class, 'artist_specialty_id');
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(SpecialtyAttributeDefinition::class, 'definition_id');
    }
}
