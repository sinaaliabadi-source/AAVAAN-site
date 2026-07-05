<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verification extends Model
{
    public const TYPE_PHONE        = 'phone';
    public const TYPE_RESUME       = 'resume';
    public const TYPE_PROFESSIONAL = 'professional';
    public const TYPE_SPECIALTY    = 'specialty';

    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id', 'artist_specialty_id', 'type', 'artist_note', 'evidence_links',
        'status', 'reviewed_at', 'notes',
    ];

    protected $casts = [
        'reviewed_at'    => 'datetime',
        'evidence_links' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function artistSpecialty(): BelongsTo
    {
        return $this->belongsTo(ArtistSpecialty::class, 'artist_specialty_id');
    }

    public function isPending(): bool  { return $this->status === self::STATUS_PENDING; }
    public function isApproved(): bool { return $this->status === self::STATUS_APPROVED; }
    public function isRejected(): bool { return $this->status === self::STATUS_REJECTED; }

    public function scopeSpecialty($query)
    {
        return $query->where('type', self::TYPE_SPECIALTY);
    }
}
