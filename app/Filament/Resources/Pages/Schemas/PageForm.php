<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Наслов')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->label('Слаг (за URL)')
                    ->required()
                    ->unique(ignoreRecord: true),
                Textarea::make('content')
                    ->label('Садржај')
                    ->columnSpanFull(),
                Select::make('parent_id')
                    ->relationship('parent', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Родитељска страница'),
                TextInput::make('order')
                    ->label('Редослед')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('Активно')
                    ->required()
                    ->default(true),
            ]);
    }
}