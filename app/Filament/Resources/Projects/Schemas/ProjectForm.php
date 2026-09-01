<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProjectForm
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
                TextInput::make('external_url')
                    ->label('Спољашњи линк')
                    ->url(),
                Toggle::make('is_active')
                    ->label('Активно')
                    ->required()
                    ->default(true),
                Select::make('image_id')
                    ->relationship('image', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Слика пројекта'),
            ]);
    }
}