<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasLabel
{
    case Admin = 'admin';
    case Profesor = 'profesor';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Администратор',
            self::Profesor => 'Професор',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Profesor => 'info',
        };
    }
}
