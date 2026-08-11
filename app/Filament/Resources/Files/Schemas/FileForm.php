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
                    ->required(),
                FileUpload::make('url')
                    ->label('Fajl')
                    ->directory('uploads')
                    ->storeFileNamesIn('file_name')
                    ->required(),
                Select::make('file_type')
                    ->options([
                        'image' => 'Slika',
                        'document' => 'Dokument',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }
}