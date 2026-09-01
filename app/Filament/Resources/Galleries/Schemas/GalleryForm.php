<?php

namespace App\Filament\Resources\Galleries\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Наслов')
                    ->required(),
                Textarea::make('description')
                    ->label('Опис')
                    ->columnSpanFull(),
                DateTimePicker::make('created_date')
                    ->label('Датум креирања')
                    ->required()
                    ->default(now()),
                Toggle::make('is_active')
                    ->label('Активно')
                    ->required()
                    ->default(true),
            ]);
    }
}