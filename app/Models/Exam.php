<?php

namespace App\Models;

use App\Enums\ExamType;
use App\Support\ExamSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

/**
 * Провера знања у распореду (контролни или писмени задатак).
 */
class Exam extends Model
{
    protected $fillable = [
        'school_class_id',
        'subject_id',
        'user_id',
        'type',
        'date',
        'period',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'type' => ExamType::class,
            'date' => 'date',
            'period' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        // Последња провера пре уписа у базу (формулар проверава исто правило раније).
        static::saving(function (Exam $exam): void {
            if (! $exam->isDirty(['date', 'school_class_id'])) {
                return;
            }

            $message = ExamSchedule::check($exam->school_class_id, $exam->date, $exam->exists ? $exam->getKey() : null);

            if ($message) {
                throw ValidationException::withMessages(['date' => $message]);
            }
        });
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
