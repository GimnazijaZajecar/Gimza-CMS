<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Категорија има највише два нивоа: надкатегорија и поткатегорија.
 */
class Category extends Model
{
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    public function getSlugSourceColumn(): string
    {
        return 'name';
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('name');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function scopeTopLevel(Builder $query): void
    {
        $query->whereNull('parent_id');
    }

    /**
     * Пун назив са надкатегоријом, нпр. „Такмичења › Информатика“.
     */
    public function getFullNameAttribute(): string
    {
        return $this->parent ? "{$this->parent->name} › {$this->name}" : $this->name;
    }
}
