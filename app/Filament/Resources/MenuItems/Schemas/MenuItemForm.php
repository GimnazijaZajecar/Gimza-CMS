<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Ознака')
                    ->required(),
                TextInput::make('url')
                    ->label('Линк')
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'label')
                    ->searchable()
                    ->preload()
                    ->label('Родитељска ставка'),
                TextInput::make('order')
                    ->label('Редослед')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_external')
                    ->label('Спољашњи линк')
                    ->required()
                    ->default(false),
                Toggle::make('is_active')
                    ->label('Активно')
                    ->required()
                    ->default(true),
            ]);
    }
}