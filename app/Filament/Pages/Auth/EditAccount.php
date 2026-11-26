<?php

namespace App\Filament\Pages\Auth;

use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Support\Fields;
use Filament\Auth\Pages\EditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Подешавања налога пријављеног корисника: корисничко име, имејл (не мења се) и телефон.
 * Лозинка може да се промени у посебном делу.
 */
class EditAccount extends EditProfile
{
    public static function getLabel(): string
    {
        return 'Подешавања налога';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Подаци о налогу')
                    ->schema([
                        Fields::username(),
                        TextInput::make('email')
                            ->label('Имејл адреса')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Имејл адреса се не мења. Ако је погрешна, обратите се администратору.'),
                        TextInput::make('phone')
                            ->label('Број телефона')
                            ->tel()
                            ->telRegex(UserForm::PHONE_REGEX)
                            ->maxLength(20),
                    ]),

                Section::make('Промена лозинке')
                    ->description('Попуните само ако желите нову лозинку.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getCurrentPasswordFormComponent(),
                    ]),
            ]);
    }

    /**
     * Само „Сачувај измене“, без дугмета „Одустани“.
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }
}
