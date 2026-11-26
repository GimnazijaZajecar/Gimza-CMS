<?php

namespace App\Filament\Resources\SchoolClasses\Pages;

use App\Filament\Resources\SchoolClasses\SchoolClassResource;
use App\Models\SchoolClass;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchoolClass extends EditRecord
{
    protected static string $resource = SchoolClassResource::class;

    public function getTitle(): string
    {
        return 'Одељење '.$this->getRecord()->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Одељење са заказаним проверама се не брише (обрисале би се и провере).
            DeleteAction::make()
                ->hidden(fn (SchoolClass $record): bool => $record->exams()->exists()),
        ];
    }
}
