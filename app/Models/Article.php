<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Concerns\DeletesStoredFiles;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\TracksEditors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Article extends Model
{
    use DeletesStoredFiles;
    use HasSlug;
    use TracksEditors;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'status',
        'category_id',
        'image',
        'is_featured',
        'event_date',
        'event_location',
        'external_url',
        'published_at',
        'created_by_id',
        'updated_by_id',
    ];

    /** @var array<string> */
    protected array $storedFiles = ['image'];

    protected function casts(): array
    {
        return [
            'type' => ArticleType::class,
            'status' => ArticleStatus::class,
            'is_featured' => 'boolean',
            'event_date' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Article $article): void {
            // Датум објаве се уписује сам, првог пута када чланак постане „Објављено“.
            if ($article->status === ArticleStatus::Objavljeno && $article->published_at === null) {
                $article->published_at = now();
            }

            // Поља која важе само за одређену врсту чланка.
            if ($article->type !== ArticleType::Dogadjaj) {
                $article->event_date = null;
                $article->event_location = null;
            }

            if ($article->type !== ArticleType::Projekat) {
                $article->external_url = null;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag')->withTimestamps();
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'article_page')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', ArticleStatus::Objavljeno);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }
}
