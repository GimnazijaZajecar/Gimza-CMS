<?php

namespace App\Filament\Resources\Files\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class FileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Наслов')
                    ->required(),
                FileUpload::make('url')
                    ->label('Фајл')
                    ->directory('uploads')
                    ->storeFileNamesIn('file_name')
                    ->required(),
                Select::make('file_type')
                    ->label('Тип фајла')
                    ->options([
                        'image' => 'Слика',
                        'document' => 'Документ',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Активно')
                    ->required()
                    ->default(true),
            ]);
    }
}