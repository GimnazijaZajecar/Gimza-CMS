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
                    ->required(),
                TextInput::make('url')
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'label')
                    ->searchable()
                    ->preload()
                    ->label('Roditeljska stavka'),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_external')
                    ->required()
                    ->default(false),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }
}