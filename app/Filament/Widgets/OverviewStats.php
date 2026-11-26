<?php

namespace App\Filament\Widgets;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Exam;
use App\Models\File;
use App\Support\ExamSchedule;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Кратак преглед на надзорној табли (администратор види више од професора).
 */
class OverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        [$weekStart, $weekEnd] = ExamSchedule::week(today());

        $examsThisWeek = Exam::query()->whereBetween('date', [$weekStart, $weekEnd])->count();

        $myUpcoming = Exam::query()
            ->where('user_id', auth()->id())
            ->whereDate('date', '>=', today())
            ->count();

        $stats = [
            Stat::make('Провере ове недеље', $examsThisWeek)
                ->description('Сва одељења')
                ->icon(Heroicon::OutlinedCalendarDays),
            Stat::make('Моје предстојеће провере', $myUpcoming)
                ->description('Које сам заказао/ла')
                ->icon(Heroicon::OutlinedClipboardDocumentCheck),
        ];

        if (auth()->user()?->isAdmin()) {
            $stats[] = Stat::make('Објављени чланци', Article::query()->where('status', ArticleStatus::Objavljeno)->count())
                ->description('Нацрта: '.Article::query()->where('status', ArticleStatus::Nacrt)->count())
                ->icon(Heroicon::OutlinedNewspaper);

            $stats[] = Stat::make('Документа на сајту', File::query()->where('is_active', true)->count())
                ->icon(Heroicon::OutlinedPaperClip);
        }

        return $stats;
    }
}
