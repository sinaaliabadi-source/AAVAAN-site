<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioVideo extends Model
{
    protected $fillable = ['artist_profile_id', 'file_path', 'duration', 'caption', 'is_reel', 'order'];

    protected $casts = [
        'is_reel' => 'boolean',
        'duration' => 'integer',
    ];

    public function artistProfile(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('uploads/reels/' . $this->file_path);
    }

    public function getFormattedDurationAttribute(): ?string
    {
        if (!$this->duration) return null;
        $m = intdiv($this->duration, 60);
        $s = $this->duration % 60;
        return sprintf('%d:%02d', $m, $s);
    }
}
