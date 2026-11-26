<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Одељење, нпр. III-2 (трећи разред, друго одељење).
 */
class SchoolClass extends Model
{
    protected $table = 'school_classes';

    protected $fillable = [
        'grade',
        'section',
        'program',
    ];

    protected function casts(): array
    {
        return [
            'grade' => 'integer',
            'section' => 'integer',
        ];
    }

    public const GRADES = [
        1 => 'I',
        2 => 'II',
        3 => 'III',
        4 => 'IV',
    ];

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('grade')->orderBy('section');
    }

    public function getNameAttribute(): string
    {
        return (self::GRADES[$this->grade] ?? $this->grade).'-'.$this->section;
    }

    /**
     * @return array<int, string> id => назив, поређано по разредима
     */
    public static function options(): array
    {
        return static::query()->ordered()->get()->mapWithKeys(fn (SchoolClass $class) => [$class->id => $class->name])->all();
    }
}
