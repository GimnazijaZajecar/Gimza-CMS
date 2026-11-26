<?php

namespace App\Models\Concerns;

use App\Support\Slug;
use Illuminate\Database\Eloquent\Model;

/**
 * Прави URL адресу (slug) од наслова ако корисник не унесе своју,
 * и додаје -2, -3... ако таква адреса већ постоји.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function (Model $model): void {
            $slug = Slug::make((string) ($model->getAttribute('slug') ?: $model->getAttribute($model->getSlugSourceColumn())));

            $model->setAttribute('slug', static::makeUniqueSlug($slug ?: 'stavka', $model));
        });
    }

    public function getSlugSourceColumn(): string
    {
        return 'title';
    }

    protected static function makeUniqueSlug(string $slug, Model $model): string
    {
        $candidate = $slug;
        $counter = 2;

        while (static::query()
            ->where('slug', $candidate)
            ->when($model->exists, fn ($query) => $query->whereKeyNot($model->getKey()))
            ->exists()) {
            $candidate = "{$slug}-{$counter}";
            $counter++;
        }

        return $candidate;
    }
}
