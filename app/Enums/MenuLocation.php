<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Мени у који се додаје ставка.
 */
enum MenuLocation: string implements HasLabel
{
    case Glavni = 'glavni';
    case Podnozje = 'podnozje';

    public function getLabel(): string
    {
        return match ($this) {
            self::Glavni => 'Главни мени (заглавље)',
            self::Podnozje => 'Мени у подножју',
        };
    }
}
