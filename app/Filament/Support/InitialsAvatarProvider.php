<?php

namespace App\Filament\Support;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;

/**
 * Аватар са иницијалима (нпр. „ЈЈ“), направљен у самом панелу.
 * Ради и без интернета, па не зависи од спољног сервиса.
 */
class InitialsAvatarProvider implements AvatarProvider
{
    public function get(Model $record): string
    {
        $name = trim(Filament::getNameForDefaultAvatar($record));

        $initials = collect(preg_split('/\s+/u', $name) ?: [])
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->join('');

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64">'
            .'<rect width="64" height="64" fill="#e0e7ff"/>'
            .'<text x="32" y="32" dy=".35em" text-anchor="middle" font-family="Arial, sans-serif" font-size="26" font-weight="600" fill="#3730a3">'
            .e($initials ?: '?')
            .'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
