<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    use DeletesStoredFiles;

    protected $fillable = [
        'gallery_id',
        'path',
        'order',
    ];

    /** @var array<string> */
    protected array $storedFiles = ['path'];

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(Gallery::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
