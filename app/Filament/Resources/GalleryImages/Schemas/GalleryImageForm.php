<?php

namespace App\Filament\Resources\GalleryImages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GalleryImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('gallery_id')
                    ->relationship('gallery', 'title')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Galerija'),
                Select::make('file_id')
                    ->relationship('file', 'title')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Slika'),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}