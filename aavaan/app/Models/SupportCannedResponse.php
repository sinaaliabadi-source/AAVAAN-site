<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportCannedResponse extends Model
{
    protected $fillable = ['title', 'department', 'content', 'created_by', 'use_count'];

    protected $casts = [
        'use_count' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
