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
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->options([
                        'vest' => 'Vest',
                        'uspeh' => 'Uspeh',
                        'dogadjaj' => 'Događaj',
                    ])
                    ->required()
                    ->live(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Kategorija'),
                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Tagovi'),
                Select::make('featured_image_id')
                    ->relationship('featuredImage', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Naslovna slika'),
                DateTimePicker::make('event_date')
                    ->visible(fn (Get $get) => $get('type') === 'dogadjaj')
                    ->label('Datum događaja'),
                TextInput::make('event_location')
                    ->visible(fn (Get $get) => $get('type') === 'dogadjaj')
                    ->label('Mesto održavanja'),
                Toggle::make('is_featured')
                    ->required(),
                Toggle::make('is_active')
                    ->required()
                    ->default(true),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
            ]);
    }
}