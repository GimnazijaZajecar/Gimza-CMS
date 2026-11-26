<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Filament\Resources\Exams\ExamResource;
use App\Models\SchoolClass;
use App\Support\ExamSchedule;
use App\Support\ExamScheduleExport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListExams extends ListRecords
{
    protected static string $resource = ExamResource::class;

    public function getSubheading(): ?string
    {
        return 'Правила: једно одељење може да има највише '.ExamSchedule::MAX_PER_DAY.' проверу дневно и највише '
            .ExamSchedule::MAX_PER_WEEK.' провере недељно, само радним данима.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Извези у Excel')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->modalHeading('Извоз распореда провера')
                ->modalSubmitActionLabel('Извези')
                ->schema([
                    DatePicker::make('from')
                        ->label('Од')
                        ->native(false)
                        ->displayFormat('d.m.Y.')
                        ->default(today())
                        ->required(),
                    DatePicker::make('to')
                        ->label('До')
                        ->native(false)
                        ->displayFormat('d.m.Y.')
                        ->default(today()->addMonths(3))
                        ->afterOrEqual('from')
                        ->required(),
                    Select::make('school_class_id')
                        ->label('Одељење')
                        ->placeholder('Сва одељења')
                        ->options(fn (): array => SchoolClass::options()),
                    Toggle::make('publish')
                        ->label('Окачи табелу на сајт (у Документа)')
                        ->helperText('Документ „Распоред писмених провера“ се замењује новом табелом, а линк на сајту остаје исти.')
                        ->visible(fn (): bool => (bool) auth()->user()?->isAdmin()),
                ])
                ->action(function (array $data): ?BinaryFileResponse {
                    $exams = ExamScheduleExport::exams(
                        Carbon::parse($data['from']),
                        Carbon::parse($data['to']),
                        filled($data['school_class_id'] ?? null) ? (int) $data['school_class_id'] : null,
                    );

                    if ($exams->isEmpty()) {
                        Notification::make()
                            ->title('У изабраном периоду нема заказаних провера.')
                            ->warning()
                            ->send();

                        return null;
                    }

                    $path = sys_get_temp_dir().'/raspored-'.Str::random(12).'.xlsx';
                    ExamScheduleExport::toXlsx($exams, $path);

                    if (($data['publish'] ?? false) && auth()->user()?->isAdmin()) {
                        ExamScheduleExport::publish($path, 'Распоред писмених провера');

                        Notification::make()
                            ->title('Табела је окачена у Документа.')
                            ->success()
                            ->send();
                    }

                    return response()
                        ->download($path, 'raspored-provera-'.now()->format('Y-m-d').'.xlsx')
                        ->deleteFileAfterSend();
                }),
            CreateAction::make()->label('Закажи проверу'),
        ];
    }
}
