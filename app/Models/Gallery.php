<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\TracksEditors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Подгалерија школске галерије (нпр. „Дан школе 2026“).
 */
class Gallery extends Model
{
    use HasSlug;
    use TracksEditors;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'album_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Брисање преко модела да би се обрисале и слике са диска.
        static::deleting(function (Gallery $gallery): void {
            $gallery->images()->get()->each->delete();
        });
    }

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)->orderBy('order');
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->images()->first()?->url;
    }
}
