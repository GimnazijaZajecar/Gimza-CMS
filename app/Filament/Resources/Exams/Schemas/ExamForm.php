<?php

namespace App\Filament\Resources\Exams\Schemas;

use App\Enums\ExamType;
use App\Enums\UserRole;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Support\ExamSchedule;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class ExamForm
{
    public const PERIODS = [
        1 => '1. час',
        2 => '2. час',
        3 => '3. час',
        4 => '4. час',
        5 => '5. час',
        6 => '6. час',
        7 => '7. час',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Провера')
                    ->schema([
                        Select::make('school_class_id')
                            ->label('Одељење')
                            ->options(fn (): array => SchoolClass::options())
                            ->searchable()
                            ->required()
                            ->live(),
                        Select::make('subject_id')
                            ->label('Предмет')
                            ->options(fn (): array => self::subjectOptions())
                            ->searchable()
                            ->required(),
                        ToggleButtons::make('type')
                            ->label('Врста провере')
                            ->options(ExamType::class)
                            ->default(ExamType::Kontrolni)
                            ->inline()
                            ->required(),
                        Select::make('user_id')
                            ->label('Професор')
                            ->options(fn (): array => User::query()
                                ->where('is_active', true)
                                ->orderBy('last_name')
                                ->get()
                                ->mapWithKeys(fn (User $user): array => [$user->id => $user->full_name])
                                ->all())
                            ->default(fn (): ?int => auth()->id())
                            ->searchable()
                            ->required()
                            ->visible(fn (): bool => (bool) auth()->user()?->isAdmin()),
                    ]),

                Section::make('Термин')
                    ->schema([
                        DatePicker::make('date')
                            ->label('Датум')
                            ->required()
                            ->native(false)
                            ->displayFormat('d.m.Y.')
                            ->firstDayOfWeek(1)
                            ->closeOnDateSelection()
                            ->minDate(fn (?Exam $record): Carbon => $record?->date?->isPast() ? $record->date : today())
                            ->maxDate(fn (): Carbon => today()->addYear())
                            ->disabledDates(fn (Get $get, ?Exam $record): array => ExamSchedule::unavailableDates(
                                filled($get('school_class_id')) ? (int) $get('school_class_id') : null,
                                today(),
                                today()->addYear(),
                                $record?->getKey(),
                            ))
                            ->helperText('Сиви дани нису доступни: викенди, дани када одељење већ има проверу и недеље у којима одељење већ има три провере.')
                            ->live()
                            ->rule(fn (Get $get, ?Exam $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                                if (blank($value) || blank($get('school_class_id'))) {
                                    return;
                                }

                                $date = Carbon::parse($value)->startOfDay();
                                $classId = (int) $get('school_class_id');

                                $isNewTerm = ! $record
                                    || ! $record->date->isSameDay($date)
                                    || $record->school_class_id !== $classId;

                                if ($isNewTerm && $date->lt(today())) {
                                    $fail('Провера не може да се закаже за дан који је прошао.');

                                    return;
                                }

                                if ($message = ExamSchedule::check($classId, $date, $record?->getKey())) {
                                    $fail($message);
                                }
                            }),
                        Select::make('period')
                            ->label('Час')
                            ->options(self::PERIODS)
                            ->placeholder('Није одређен'),
                        TextInput::make('note')
                            ->label('Напомена (тема)')
                            ->placeholder('нпр. Квадратне једначине')
                            ->maxLength(255),
                    ]),
            ]);
    }

    /**
     * Професор бира међу предметима које предаје; ако их нема унетих, види све.
     *
     * @return array<int, string>
     */
    public static function subjectOptions(): array
    {
        $user = auth()->user();

        $query = ($user && $user->role === UserRole::Profesor && $user->subjects()->exists())
            ? $user->subjects()
            : Subject::query();

        return $query->orderBy('name')->pluck('subjects.name', 'subjects.id')->all();
    }
}
