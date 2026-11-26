<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Наставни предмет (нпр. Математика).
 */
class Subject extends Model
{
    protected $fillable = [
        'name',
    ];

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'staff_subject', 'subject_id', 'staff_id')->withTimestamps();
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
