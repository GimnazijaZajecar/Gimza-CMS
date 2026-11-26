<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Брише стари фајл са диска када се замени или када се запис обрише,
 * да на диску не остају фајлови којих нема у бази.
 *
 * Модел наводи колоне са путањама у својству $storedFiles.
 */
trait DeletesStoredFiles
{
    public static function bootDeletesStoredFiles(): void
    {
        static::updated(function (Model $model): void {
            foreach ($model->getStoredFileColumns() as $column) {
                if (! array_key_exists($column, $model->getChanges())) {
                    continue;
                }

                $old = $model->getOriginal($column);

                if (filled($old) && $old !== $model->getAttribute($column)) {
                    Storage::disk('public')->delete($old);
                }
            }
        });

        static::deleted(function (Model $model): void {
            foreach ($model->getStoredFileColumns() as $column) {
                if (filled($path = $model->getAttribute($column))) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }

    /**
     * @return array<string>
     */
    public function getStoredFileColumns(): array
    {
        return $this->storedFiles ?? [];
    }
}
