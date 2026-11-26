<?php

namespace App\Support;

use App\Models\Exam;
use App\Models\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

/**
 * Прави Excel табелу распореда провера и, по жељи, качи је у Документа.
 */
class ExamScheduleExport
{
    public const PUBLISHED_SLUG = 'raspored-provera';

    /**
     * @param  Collection<int, Exam>  $exams
     */
    public static function toXlsx(Collection $exams, string $targetPath): string
    {
        $options = new Options;
        $options->setColumnWidth(13, 1);
        $options->setColumnWidth(12, 2);
        $options->setColumnWidth(10, 3);
        $options->setColumnWidth(30, 4);
        $options->setColumnWidth(20, 5);
        $options->setColumnWidth(7, 6);
        $options->setColumnWidth(26, 7);
        $options->setColumnWidth(40, 8);

        $writer = new Writer($options);
        $writer->openToFile($targetPath);
        $writer->getCurrentSheet()->setName('Распоред провера');

        $header = (new Style)->setFontBold()->setBackgroundColor('E0E7FF');

        $writer->addRow(Row::fromValues(['Датум', 'Дан', 'Одељење', 'Предмет', 'Врста провере', 'Час', 'Професор', 'Напомена'], $header));

        foreach ($exams as $exam) {
            $date = Carbon::parse($exam->date)->locale('sr_Cyrl');

            $writer->addRow(Row::fromValues([
                $date->format('d.m.Y.'),
                $date->isoFormat('dddd'),
                $exam->schoolClass?->name,
                $exam->subject?->name,
                $exam->type->getLabel(),
                $exam->period ? "{$exam->period}." : '',
                $exam->professor?->full_name,
                (string) $exam->note,
            ]));
        }

        $writer->close();

        return $targetPath;
    }

    /**
     * @return Collection<int, Exam>
     */
    public static function exams(Carbon $from, Carbon $to, ?int $schoolClassId = null): Collection
    {
        return Exam::query()
            ->with(['schoolClass', 'subject', 'professor'])
            ->whereBetween('date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->when($schoolClassId, fn ($query) => $query->where('school_class_id', $schoolClassId))
            ->join('school_classes', 'school_classes.id', '=', 'exams.school_class_id')
            ->orderBy('exams.date')
            ->orderBy('school_classes.grade')
            ->orderBy('school_classes.section')
            ->select('exams.*')
            ->get();
    }

    /**
     * Качи табелу у Документа (увек исти запис, па линк на сајту остаје исти).
     */
    public static function publish(string $xlsxPath, string $title): File
    {
        $storedPath = 'dokumenta/'.self::PUBLISHED_SLUG.'-'.now()->format('Y-m-d-His').'.xlsx';

        Storage::disk('public')->put($storedPath, file_get_contents($xlsxPath), 'public');

        $file = File::query()->firstOrNew(['slug' => self::PUBLISHED_SLUG]);

        $file->fill([
            'title' => $title,
            'path' => $storedPath,
            'file_name' => self::PUBLISHED_SLUG.'.xlsx',
            'is_active' => true,
        ])->save();

        return $file;
    }
}
