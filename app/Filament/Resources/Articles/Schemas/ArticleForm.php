<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Models\Tag;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ArticleForm
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
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Тип')
                    ->options([
                        'vest' => 'Вест',
                        'uspeh' => 'Успех',
                        'dogadjaj' => 'Догађај',
                    ])
                    ->required()
                    ->live(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Категорија'),
                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Тагови'),
                Select::make('featured_image_id')
                    ->relationship('featuredImage', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Насловна слика'),
                DateTimePicker::make('event_date')
                    ->visible(fn (Get $get) => $get('type') === 'dogadjaj')
                    ->label('Датум догађаја'),
                TextInput::make('event_location')
                    ->visible(fn (Get $get) => $get('type') === 'dogadjaj')
                    ->label('Место одржавања'),
                Toggle::make('is_featured')
                    ->label('Издвојено')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Активно')
                    ->required()
                    ->default(true),
                TextInput::make('status')
                    ->label('Статус')
                    ->required()
                    ->default('draft'),
            ]);
    }
}