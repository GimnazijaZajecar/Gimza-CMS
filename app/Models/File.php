<?php

namespace App\Models;

use App\Models\Concerns\DeletesStoredFiles;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\TracksEditors;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

/**
 * Документ за преузимање. Врста фајла се чита из екстензије,
 * а ко је отпремио и изменио фајл уписује се аутоматски.
 */
class File extends Model
{
    use DeletesStoredFiles;
    use HasSlug;
    use TracksEditors;

    protected $fillable = [
        'title',
        'slug',
        'path',
        'file_name',
        'size',
        'is_active',
        'uploaded_by_id',
        'updated_by_id',
    ];

    /** @var array<string> */
    protected array $storedFiles = ['path'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'size' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (File $file): void {
            if ($file->isDirty('path') && filled($file->path) && Storage::disk('public')->exists($file->path)) {
                $file->size = Storage::disk('public')->size($file->path);
            }

            if (blank($file->file_name) && filled($file->path)) {
                $file->file_name = basename($file->path);
            }
        });
    }

    public function getCreatorColumn(): string
    {
        return 'uploaded_by_id';
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->creator();
    }

    public function updatedBy(): BelongsTo
    {
        return $this->updater();
    }

    /**
     * Екстензија великим словима, нпр. PDF или DOCX.
     */
    public function getExtensionAttribute(): string
    {
        return strtoupper(pathinfo($this->file_name ?: $this->path, PATHINFO_EXTENSION));
    }

    public function getUrlAttribute(): ?string
    {
        return $this->path ? Storage::disk('public')->url($this->path) : null;
    }

    public function getHumanSizeAttribute(): ?string
    {
        return $this->size ? Number::fileSize($this->size, maxPrecision: 1) : null;
    }
}
