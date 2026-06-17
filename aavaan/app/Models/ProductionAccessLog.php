<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionAccessLog extends Model
{
    protected $fillable = ['production_access_id', 'production_user_id', 'artist_profile_id', 'accessed_at'];

    protected $casts = ['accessed_at' => 'datetime'];

    public function productionAccess(): BelongsTo
    {
        return $this->belongsTo(ProductionAccess::class);
    }

    public function artistProfile(): BelongsTo
    {
        return $this->belongsTo(ArtistProfile::class);
    }

    public function productionUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'production_user_id');
    }
}
