<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkHistory extends Model
{
    protected $fillable = ['artist_profile_id', 'title', 'role', 'year', 'director', 'description', 'order'];

    protected $casts = ['year' => 'integer', 'order' => 'integer'];

    public function artistProfile(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class);
    }
}
