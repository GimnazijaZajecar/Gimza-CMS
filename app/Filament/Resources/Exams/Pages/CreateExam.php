<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Filament\Resources\Exams\ExamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExam extends CreateRecord
{
    protected static string $resource = ExamResource::class;

    protected static ?string $title = 'Закажи проверу';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Професор увек заказује у своје име; администратор бира професора.
        if (! auth()->user()?->isAdmin()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}
