<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected ?string $subheading = 'Страница нема свој текст: у њеном формулару бирате чланке који се на њој приказују.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Нова страница'),
        ];
    }
}
