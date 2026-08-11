<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('address')
                    ->label('Adresa'),
                TextInput::make('phone')
                    ->tel()
                    ->label('Telefon'),
                TextInput::make('email')
                    ->email()
                    ->label('Email'),
                TextInput::make('working_hours')
                    ->label('Radno vreme'),
                TextInput::make('facebook_url')
                    ->url()
                    ->label('Facebook link'),
                TextInput::make('instagram_url')
                    ->url()
                    ->label('Instagram link'),
            ]);
    }
}