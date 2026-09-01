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
                    ->label('Адреса'),
                TextInput::make('phone')
                    ->tel()
                    ->label('Телефон'),
                TextInput::make('email')
                    ->email()
                    ->label('Имејл'),
                TextInput::make('working_hours')
                    ->label('Радно време'),
                TextInput::make('facebook_url')
                    ->url()
                    ->label('Facebook линк'),
                TextInput::make('instagram_url')
                    ->url()
                    ->label('Instagram линк'),
            ]);
    }
}