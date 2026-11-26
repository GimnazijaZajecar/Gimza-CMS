<?php

namespace App\Filament\Resources\Galleries\Pages;

use App\Filament\Resources\Galleries\GalleryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGalleries extends ListRecords
{
    protected static string $resource = GalleryResource::class;

    protected ?string $subheading = 'Сајт има једну галерију. Овде се праве њене подгалерије (албуми), а слике се додају у сваку подгалерију.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Нова подгалерија'),
        ];
    }
}
