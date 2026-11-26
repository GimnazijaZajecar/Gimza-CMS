<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Filament\Support\Fields;
use App\Models\Article;
use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Садржај')
                    ->columnSpan(2)
                    ->schema([
                        Fields::title(),
                        Fields::slug(),
                        RichEditor::make('description')
                            ->label('Текст')
                            ->required()
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'link'],
                                ['h2', 'h3'],
                                ['bulletList', 'orderedList', 'blockquote'],
                                ['undo', 'redo'],
                            ])
                            ->fileAttachments(false)
                            ->columnSpanFull(),
                        Fields::image('image', 'clanci', 'Насловна слика'),
                    ]),

                Section::make('Подаци о чланку')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('type')
                            ->label('Врста')
                            ->options(ArticleType::class)
                            ->required()
                            ->live(),
                        Select::make('status')
                            ->label('Статус')
                            ->options(ArticleStatus::class)
                            ->default(ArticleStatus::Nacrt)
                            ->selectablePlaceholder(false)
                            ->required(),
                        Toggle::make('is_featured')
                            ->label('Издвојено на почетној страни'),

                        // Категорија се бира као у веб продавници: прво категорија, па поткатегорија.
                        Hidden::make('category_id'),
                        Select::make('category_main')
                            ->label('Категорија')
                            ->options(fn (): array => Category::query()->topLevel()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Select $component, Get $get): void {
                                $category = Category::find($get('category_id'));
                                $component->state($category?->parent_id ?? $category?->id);
                            })
                            ->afterStateUpdated(function (Set $set, $state): void {
                                $set('category_sub', null);
                                $set('category_id', $state);
                            }),
                        Select::make('category_sub')
                            ->label('Поткатегорија')
                            ->options(fn (Get $get): array => Category::query()
                                ->where('parent_id', $get('category_main'))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->placeholder('Без поткатегорије')
                            ->visible(fn (Get $get): bool => filled($get('category_main'))
                                && Category::query()->where('parent_id', $get('category_main'))->exists())
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Select $component, Get $get): void {
                                $category = Category::find($get('category_id'));
                                $component->state($category?->parent_id ? $category->id : null);
                            })
                            ->afterStateUpdated(fn (Set $set, Get $get, $state) => $set('category_id', $state ?: $get('category_main'))),

                        Select::make('tags')
                            ->label('Ознаке')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Нову ознаку додајте дугметом + поред листе.')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Назив ознаке')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique('tags', 'name'),
                            ])
                            ->createOptionModalHeading('Нова ознака'),

                        TextEntry::make('published_info')
                            ->label('Датум објаве')
                            ->state(fn (?Article $record): string => $record?->published_at?->format('d.m.Y. H:i')
                                ?? 'Уписује се сам када чланак постане „Објављено“.'),
                    ]),

                Section::make('Догађај')
                    ->columnSpan(2)
                    ->columns(2)
                    ->visible(fn (Get $get): bool => self::isType($get('type'), ArticleType::Dogadjaj))
                    ->schema([
                        DateTimePicker::make('event_date')
                            ->label('Датум и време догађаја')
                            ->seconds(false)
                            ->required(),
                        TextInput::make('event_location')
                            ->label('Место одржавања')
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Пројекат')
                    ->columnSpan(2)
                    ->visible(fn (Get $get): bool => self::isType($get('type'), ArticleType::Projekat))
                    ->schema([
                        TextInput::make('external_url')
                            ->label('Линк пројекта')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://'),
                    ]),
            ]);
    }

    public static function isType(mixed $state, ArticleType $type): bool
    {
        $state = $state instanceof ArticleType ? $state : ArticleType::tryFrom((string) $state);

        return $state === $type;
    }
}
