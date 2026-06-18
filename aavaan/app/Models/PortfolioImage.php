<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioImage extends Model
{
    protected $fillable = ['artist_profile_id', 'file_path', 'caption', 'order'];

    public function artistProfile(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('uploads/portfolios/' . $this->file_path);
    }
}
