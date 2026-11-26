<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected ?string $subheading = 'Категорије имају два нивоа, као у веб продавници: надкатегорија (нпр. Такмичења) и поткатегорија (нпр. Информатика).';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Нова категорија'),
        ];
    }
}
