<?php

namespace App\Support;

use App\Models\Exam;
use App\Models\SchoolClass;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

/**
 * Правила распореда провера знања за једно одељење:
 *  - највише MAX_PER_DAY провера дневно (контролни или писмени),
 *  - највише MAX_PER_WEEK провера у недељи (понедељак–недеља),
 *  - само радним данима и не у прошлости.
 *
 * Правила се проверавају и у формулару (календар не нуди заузете дане)
 * и на серверу пре чувања.
 */
class ExamSchedule
{
    public const MAX_PER_DAY = 1;

    public const MAX_PER_WEEK = 3;

    /**
     * Враћа поруку о грешци или null ако провера може да се закаже.
     */
    public static function check(int $schoolClassId, CarbonInterface|string $date, ?int $ignoreExamId = null): ?string
    {
        $date = Carbon::parse($date)->startOfDay();

        if ($date->isWeekend()) {
            return 'Провера може да се закаже само радним даном (понедељак–петак).';
        }

        $class = SchoolClass::find($schoolClassId);
        $className = $class?->name ?? '';

        $sameDay = static::query($schoolClassId, $ignoreExamId)
            ->whereDate('date', $date)
            ->with('subject')
            ->get();

        if ($sameDay->count() >= self::MAX_PER_DAY) {
            $exam = $sameDay->first();

            return "Одељење {$className} већ има {$exam->type->getLabel()} ({$exam->subject?->name}) {$date->format('d.m.Y.')} Дозвољена је једна провера дневно.";
        }

        [$weekStart, $weekEnd] = static::week($date);

        $inWeek = static::query($schoolClassId, $ignoreExamId)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->count();

        if ($inWeek >= self::MAX_PER_WEEK) {
            return "Одељење {$className} већ има {$inWeek} провере у недељи од {$weekStart->format('d.m.')} до {$weekEnd->format('d.m.Y.')} Дозвољене су највише три провере недељно.";
        }

        return null;
    }

    /**
     * Дани које календар у формулару не треба да нуди за изабрано одељење:
     * викенди, дани који већ имају проверу и дани у пуним недељама.
     *
     * @return array<string> датуми у формату Y-m-d
     */
    public static function unavailableDates(?int $schoolClassId, CarbonInterface $from, CarbonInterface $to, ?int $ignoreExamId = null): array
    {
        $taken = [];
        $perDay = collect();
        $perWeek = collect();

        if ($schoolClassId) {
            $exams = static::query($schoolClassId, $ignoreExamId)
                ->whereBetween('date', [
                    $from->copy()->startOfWeek(CarbonInterface::MONDAY)->startOfDay(),
                    $to->copy()->endOfWeek(CarbonInterface::SUNDAY)->endOfDay(),
                ])
                ->pluck('date');

            $perDay = $exams->countBy(fn (CarbonInterface $date) => $date->format('Y-m-d'));
            $perWeek = $exams->countBy(fn (CarbonInterface $date) => $date->format('o-W'));
        }

        foreach (CarbonPeriod::create($from, $to) as $day) {
            $key = $day->format('Y-m-d');

            if ($day->isWeekend()
                || (($perDay[$key] ?? 0) >= self::MAX_PER_DAY)
                || (($perWeek[$day->format('o-W')] ?? 0) >= self::MAX_PER_WEEK)) {
                $taken[] = $key;
            }
        }

        return $taken;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function week(CarbonInterface $date): array
    {
        $date = Carbon::parse($date);

        return [
            $date->copy()->startOfWeek(CarbonInterface::MONDAY)->startOfDay(),
            $date->copy()->endOfWeek(CarbonInterface::SUNDAY)->endOfDay(),
        ];
    }

    protected static function query(int $schoolClassId, ?int $ignoreExamId)
    {
        return Exam::query()
            ->where('school_class_id', $schoolClassId)
            ->when($ignoreExamId, fn ($query) => $query->whereKeyNot($ignoreExamId));
    }
}
