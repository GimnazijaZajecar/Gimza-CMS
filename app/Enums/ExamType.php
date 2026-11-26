<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ExamType: string implements HasColor, HasLabel
{
    case Kontrolni = 'kontrolni';
    case Pismeni = 'pismeni';

    public function getLabel(): string
    {
        return match ($this) {
            self::Kontrolni => 'Контролни задатак',
            self::Pismeni => 'Писмени задатак',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Kontrolni => 'info',
            self::Pismeni => 'danger',
        };
    }
}
