<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Аутоматски бележи ко је направио и ко је последњи изменио запис.
 * Поља се не уносе ручно у формулару.
 */
trait TracksEditors
{
    public static function bootTracksEditors(): void
    {
        static::creating(function (Model $model): void {
            $userId = auth()->id();

            if ($userId && blank($model->getAttribute($model->getCreatorColumn()))) {
                $model->setAttribute($model->getCreatorColumn(), $userId);
            }
        });

        static::updating(function (Model $model): void {
            if ($userId = auth()->id()) {
                $model->setAttribute($model->getUpdaterColumn(), $userId);
            }
        });
    }

    public function getCreatorColumn(): string
    {
        return 'created_by_id';
    }

    public function getUpdaterColumn(): string
    {
        return 'updated_by_id';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->getCreatorColumn());
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, $this->getUpdaterColumn());
    }
}
