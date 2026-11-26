<?php

namespace App\Models;

use App\Enums\MenuLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu',
        'label',
        'page_id',
        'url',
        'parent_id',
        'order',
        'is_external',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'menu' => MenuLocation::class,
            'is_external' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // Ставка води или на страницу или на линк, никад на оба.
        static::saving(function (MenuItem $item): void {
            if ($item->page_id) {
                $item->url = null;
                $item->is_external = false;
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function scopeInMenu(Builder $query, MenuLocation $menu): void
    {
        $query->where('menu', $menu);
    }

    /**
     * Адреса на коју ставка води: страница из CMS-а или унет линк.
     */
    public function getHrefAttribute(): ?string
    {
        return $this->page ? $this->page->path : $this->url;
    }
}
