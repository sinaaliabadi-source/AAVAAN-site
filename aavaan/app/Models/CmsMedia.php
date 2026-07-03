<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CmsMedia extends Model
{
    public $timestamps = false;

    protected $table = 'cms_media';

    protected $fillable = [
        'uploader_id', 'file_path', 'original_name', 'alt_text',
        'file_size', 'mime_type', 'width', 'height', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'file_size'  => 'integer',
        'width'      => 'integer',
        'height'     => 'integer',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function getUrlAttribute(): string
    {
        return Str::startsWith($this->file_path, ['http://', 'https://', '/'])
            ? $this->file_path
            : asset($this->file_path);
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function getHumanSizeAttribute(): string
    {
        $b = $this->file_size;
        if ($b >= 1048576) return round($b / 1048576, 1) . ' MB';
        if ($b >= 1024) return round($b / 1024) . ' KB';
        return $b . ' B';
    }
}
