<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Категорије се не бришу, па на овој страни нема дугмета „Обриши“.
 */
class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;
}
