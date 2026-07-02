<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistSpecialtyMedia extends Model
{
    public const TYPE_PHOTO      = 'photo';
    public const TYPE_VIDEO_LINK = 'video_link';
    public const TYPE_DOCUMENT   = 'document';

    public const APARAT_URL_PATTERN = '/^https:\/\/(www\.)?aparat\.com\/v\/[A-Za-z0-9]+/';

    protected $table = 'artist_specialty_media';

    protected $fillable = [
        'artist_specialty_id', 'type', 'file_path', 'external_url', 'title', 'sort_order',
    ];

    public function artistSpecialty(): BelongsTo
    {
        return $this->belongsTo(ArtistSpecialty::class);
    }

    public function getAparatEmbedUrlAttribute(): ?string
    {
        if ($this->type !== self::TYPE_VIDEO_LINK || ! $this->external_url) {
            return null;
        }

        preg_match('/aparat\.com\/v\/([A-Za-z0-9]+)/', $this->external_url, $m);
        if (! isset($m[1])) {
            return null;
        }

        return "https://www.aparat.com/video/video/embed/videohash/{$m[1]}/vt/frame";
    }
}
