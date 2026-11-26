<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Enums\MenuLocation;
use App\Models\MenuItem;
use App\Models\Page;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Ставка менија')
                    ->columnSpan(2)
                    ->schema([
                        ToggleButtons::make('menu')
                            ->label('У који мени се додаје')
                            ->options(MenuLocation::class)
                            ->default(MenuLocation::Glavni)
                            ->inline()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('parent_id', null)),
                        TextInput::make('label')
                            ->label('Назив ставке')
                            ->required()
                            ->maxLength(60),
                        ToggleButtons::make('link_type')
                            ->label('Ставка води на')
                            ->options([
                                'stranica' => 'Страницу са сајта',
                                'link' => 'Произвољан линк',
                            ])
                            ->default('stranica')
                            ->inline()
                            ->live()
                            ->dehydrated(false)
                            ->afterStateUpdated(function (Set $set, ?string $state): void {
                                if ($state === 'stranica') {
                                    $set('url', null);
                                    $set('is_external', false);
                                } else {
                                    $set('page_id', null);
                                }
                            })
                            ->afterStateHydrated(function (ToggleButtons $component, ?MenuItem $record): void {
                                if ($record) {
                                    $component->state($record->page_id ? 'stranica' : 'link');
                                }
                            }),
                        Select::make('page_id')
                            ->label('Страница')
                            ->options(fn (): array => Page::query()->orderBy('title')->pluck('title', 'id')->all())
                            ->searchable()
                            ->required(fn (Get $get): bool => $get('link_type') === 'stranica')
                            ->visible(fn (Get $get): bool => $get('link_type') === 'stranica')
                            ->dehydratedWhenHidden(),
                        TextInput::make('url')
                            ->label('Линк')
                            ->placeholder('https://… или /vesti')
                            ->helperText('Пун линк (https://…) или путања на сајту која почиње косом цртом (нпр. /galerija).')
                            ->required(fn (Get $get): bool => $get('link_type') === 'link')
                            ->maxLength(255)
                            ->rule(fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                if (filled($value) && ! str_starts_with($value, '/') && ! filter_var($value, FILTER_VALIDATE_URL)) {
                                    $fail('Линк мора да почиње са https:// или косом цртом (/).');
                                }
                            })
                            ->visible(fn (Get $get): bool => $get('link_type') === 'link')
                            ->dehydratedWhenHidden(),
                        Toggle::make('is_external')
                            ->label('Отвори у новом прозору')
                            ->visible(fn (Get $get): bool => $get('link_type') === 'link')
                            ->dehydratedWhenHidden(),
                    ]),

                Section::make('Положај')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('parent_id')
                            ->label('Надкатегорија')
                            ->placeholder('Нема – прва линија менија')
                            ->helperText('Изаберите ако ставка треба да буде у падајућем менију друге ставке.')
                            ->options(fn (Get $get, ?MenuItem $record): array => MenuItem::query()
                                ->where('menu', self::menuValue($get('menu')))
                                ->whereNull('parent_id')
                                ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->orderBy('order')
                                ->pluck('label', 'id')
                                ->all())
                            ->disabled(fn (?MenuItem $record): bool => (bool) $record?->children()->exists())
                            ->rule(fn (Get $get, ?MenuItem $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                                if (blank($value)) {
                                    return;
                                }

                                $parent = MenuItem::find($value);

                                if (! $parent || ($record && $parent->is($record))) {
                                    $fail('Ставка не може бити сама себи надкатегорија.');
                                } elseif ($parent->parent_id !== null) {
                                    $fail('Мени има највише два нивоа.');
                                } elseif ($parent->menu->value !== self::menuValue($get('menu'))) {
                                    $fail('Надкатегорија мора бити у истом менију.');
                                }
                            }),
                        TextInput::make('order')
                            ->label('Редослед')
                            ->helperText('Мањи број = више лево или горе у менију.')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(999)
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Видљиво на сајту')
                            ->default(true),
                    ]),
            ]);
    }

    public static function menuValue(mixed $state): ?string
    {
        return $state instanceof MenuLocation ? $state->value : $state;
    }
}
