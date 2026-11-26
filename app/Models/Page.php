<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Страница сајта. Нема свој текст: приказује чланке изабране у формулару.
 */
class Page extends Model
{
    use HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'parent_id',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('order');
    }

    /**
     * Чланци на страници, поређани редоследом из формулара.
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_page')
            ->withPivot('order')
            ->withTimestamps()
            ->orderByPivot('order');
    }

    /**
     * Ставке везе страница–чланак (користи их формулар за редослед).
     */
    public function pageArticles(): HasMany
    {
        return $this->hasMany(PageArticle::class)->orderBy('order');
    }

    public function getPathAttribute(): string
    {
        return '/'.$this->slug;
    }
}
