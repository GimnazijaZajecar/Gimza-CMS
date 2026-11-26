<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Врста чланка. Пројекти и текстови за странице су такође чланци.
 */
enum ArticleType: string implements HasColor, HasLabel
{
    case Vest = 'vest';
    case Uspeh = 'uspeh';
    case Dogadjaj = 'dogadjaj';
    case Projekat = 'projekat';
    case Tekst = 'tekst';

    public function getLabel(): string
    {
        return match ($this) {
            self::Vest => 'Вест',
            self::Uspeh => 'Успех',
            self::Dogadjaj => 'Догађај',
            self::Projekat => 'Пројекат',
            self::Tekst => 'Текст за страницу',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Vest => 'info',
            self::Uspeh => 'success',
            self::Dogadjaj => 'warning',
            self::Projekat => 'primary',
            self::Tekst => 'gray',
        };
    }
}
