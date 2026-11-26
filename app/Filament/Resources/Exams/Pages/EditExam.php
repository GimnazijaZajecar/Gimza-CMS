<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Filament\Resources\Exams\ExamResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExam extends EditRecord
{
    protected static string $resource = ExamResource::class;

    public function getTitle(): string
    {
        $exam = $this->getRecord();

        return "{$exam->type->getLabel()} – {$exam->schoolClass?->name}, {$exam->date->format('d.m.Y.')}";
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
