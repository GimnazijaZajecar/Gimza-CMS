<?php

namespace App\Filament\Resources\Exams;

use App\Filament\Resources\Exams\Pages\CreateExam;
use App\Filament\Resources\Exams\Pages\EditExam;
use App\Filament\Resources\Exams\Pages\ListExams;
use App\Filament\Resources\Exams\Schemas\ExamForm;
use App\Filament\Resources\Exams\Tables\ExamsTable;
use App\Models\Exam;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Распоред контролних и писмених задатака.
 * Сваки професор заказује и мења своје провере; администратор све.
 */
class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Школа';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Распоред провера';

    protected static ?string $modelLabel = 'провера';

    protected static ?string $pluralModelLabel = 'Распоред провера';

    // „Распоред провера“, а не „Распоред Провера“.
    protected static bool $hasTitleCaseModelLabel = false;

    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        $user = auth()->user();

        if (! $user) {
            return Response::deny();
        }

        if ($user->isAdmin()) {
            return Response::allow();
        }

        $ability = $action instanceof BackedEnum ? $action->value : ($action instanceof UnitEnum ? $action->name : $action);

        if (in_array($ability, ['viewAny', 'create', 'deleteAny'], true)) {
            return Response::allow();
        }

        if (in_array($ability, ['view', 'update', 'delete'], true) && $record instanceof Exam) {
            return $record->user_id === $user->getKey()
                ? Response::allow()
                : Response::deny('Можете да мењате само провере које сте ви заказали.');
        }

        return Response::deny();
    }

    public static function form(Schema $schema): Schema
    {
        return ExamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExamsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExams::route('/'),
            'create' => CreateExam::route('/create'),
            'edit' => EditExam::route('/{record}/edit'),
        ];
    }
}
