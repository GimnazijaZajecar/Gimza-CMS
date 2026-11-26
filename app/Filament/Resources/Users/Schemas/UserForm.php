<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\UserRole;
use App\Filament\Support\Fields;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public const PHONE_REGEX = '/^\+?[0-9][0-9 \/()-]{5,19}$/';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Лични подаци')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Име')
                            ->required()
                            ->maxLength(60),
                        TextInput::make('last_name')
                            ->label('Презиме')
                            ->required()
                            ->maxLength(60),
                        TextInput::make('title')
                            ->label('Звање')
                            ->placeholder('нпр. професор математике')
                            ->maxLength(120),
                        Fields::image('photo', 'korisnici', 'Фотографија')
                            ->avatar(),
                    ]),

                Section::make('Налог')
                    ->schema([
                        Fields::username(),
                        Fields::validateOnBlur(
                            TextInput::make('email')
                                ->label('Имејл адреса')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                        ),
                        TextInput::make('phone')
                            ->label('Број телефона')
                            ->tel()
                            ->telRegex(self::PHONE_REGEX)
                            ->maxLength(20),
                        ToggleButtons::make('role')
                            ->label('Улога')
                            ->options(UserRole::class)
                            ->default(UserRole::Profesor)
                            ->inline()
                            ->required(),
                        Select::make('subjects')
                            ->label('Предмети које предаје')
                            ->relationship('subjects', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        Toggle::make('is_active')
                            ->label('Налог је активан')
                            ->helperText('Неактиван корисник не може да се пријави.')
                            ->default(true),
                        TextInput::make('password')
                            ->label(fn (string $operation): string => $operation === 'create' ? 'Лозинка' : 'Нова лозинка')
                            ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Оставите празно ако се лозинка не мења.' : null)
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->rule(Password::min(8))
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                    ]),
            ]);
    }
}
