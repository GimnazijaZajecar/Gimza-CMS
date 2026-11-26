<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ArticleStatus: string implements HasColor, HasLabel
{
    case Nacrt = 'nacrt';
    case Objavljeno = 'objavljeno';
    case Arhivirano = 'arhivirano';

    public function getLabel(): string
    {
        return match ($this) {
            self::Nacrt => 'Нацрт',
            self::Objavljeno => 'Објављено',
            self::Arhivirano => 'Архивирано',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Nacrt => 'gray',
            self::Objavljeno => 'success',
            self::Arhivirano => 'warning',
        };
    }
}
