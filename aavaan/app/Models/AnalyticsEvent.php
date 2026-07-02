<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsEvent extends Model
{
    public const UPDATED_AT = null;

    public const EVENT_VIEW     = 'view';
    public const EVENT_SAVE     = 'save';
    public const EVENT_INVITE   = 'invite';
    public const EVENT_RESPONSE = 'response';

    protected $fillable = [
        'artist_id', 'event_type', 'production_team_id',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'artist_id');
    }

    public function productionTeam(): BelongsTo
    {
        return $this->belongsTo(User::class, 'production_team_id');
    }
}
