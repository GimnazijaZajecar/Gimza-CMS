<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * URL адреса (slug) по српским правилима пресловљавања:
 * „Распоред писмених провера“ → raspored-pismenih-provera, „Ђачки“ → djacki.
 * Подразумевани Laravel Str::slug() би „х“ претворио у „x“.
 */
class Slug
{
    public static function make(?string $text, string $separator = '-'): string
    {
        return Str::slug((string) $text, $separator, 'sr');
    }
}
