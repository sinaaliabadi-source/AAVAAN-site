<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtistProfilePremium extends Model
{
    protected $table = 'artist_profile_premium';

    protected $fillable = [
        'user_id',
        'stage_name', 'legal_name',
        'willing_to_travel', 'willing_long_stay',
        'military_status', 'passport_status', 'international_collaboration',
        'completed_projects_count', 'published_projects_count',
        'awards', 'memberships',
        'availability_status', 'available_from_date', 'concurrent_capacity',
        'day_rate_min', 'day_rate_max', 'show_day_rate',
        'imdb_url', 'instagram_url', 'linkedin_url',
        'youtube_url', 'vimeo_url', 'website_url',
    ];

    protected function casts(): array
    {
        return [
            'willing_to_travel'       => 'boolean',
            'willing_long_stay'       => 'boolean',
            'international_collaboration' => 'boolean',
            'awards'                  => 'array',
            'memberships'             => 'array',
            'show_day_rate'           => 'boolean',
            'available_from_date'     => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
