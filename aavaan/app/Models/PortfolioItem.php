<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    protected $fillable = ['artist_profile_id', 'type', 'file_path', 'video_url', 'caption', 'order'];

    public function artistProfile(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class);
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->type === 'image' && $this->file_path) {
            return asset('uploads/portfolios/' . $this->file_path);
        }
        return $this->video_url;
    }
}
