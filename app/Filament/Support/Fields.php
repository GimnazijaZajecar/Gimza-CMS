<?php

namespace App\Filament\Support;

use App\Support\Slug;
use App\Support\WebpImage;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Livewire\Component as Livewire;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Поља која се понављају у више формулара.
 */
class Fields
{
    /**
     * Валидација одмах када корисник напусти поље (без чекања на „Сачувај“).
     */
    public static function validateOnBlur(Field $field): Field
    {
        return $field
            ->live(onBlur: true)
            ->afterStateUpdated(function (Livewire $livewire, Field $component): void {
                $livewire->validateOnly($component->getStatePath());
            });
    }

    /**
     * Наслов који при прављењу новог записа сам попуњава URL адресу.
     */
    public static function title(string $name = 'title', string $label = 'Наслов'): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->required()
            ->minLength(3)
            ->maxLength(255)
            ->live(onBlur: true)
            ->afterStateUpdated(function (Livewire $livewire, TextInput $component, Get $get, Set $set, ?string $state, string $operation): void {
                $livewire->validateOnly($component->getStatePath());

                if ($operation === 'create' || blank($get('slug'))) {
                    $set('slug', Slug::make($state));
                }
            });
    }

    /**
     * URL адреса (slug) – део адресе на сајту, нпр. /vesti/dan-skole.
     */
    public static function slug(): TextInput
    {
        return static::validateOnBlur(
            TextInput::make('slug')
                ->label('URL адреса')
                ->helperText('Попуњава се сама од наслова. Дозвољена су мала латинична слова, бројеви и цртице.')
                ->required()
                ->maxLength(255)
                ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                ->validationMessages([
                    'regex' => 'URL адреса сме да садржи само мала латинична слова, бројеве и цртице (нпр. dan-skole-2026).',
                ])
                ->unique(ignoreRecord: true)
        );
    }

    /**
     * Корисничко име: латинична слова, бројеви, тачка, цртица и доња црта (нпр. jelena.jovanovic).
     */
    public static function username(): TextInput
    {
        return static::validateOnBlur(
            TextInput::make('username')
                ->label('Корисничко име')
                ->required()
                ->minLength(3)
                ->maxLength(50)
                ->regex('/^[A-Za-z0-9._-]+$/')
                ->validationMessages([
                    'regex' => 'Корисничко име сме да садржи само латинична слова, бројеве, тачку, цртицу и доњу црту.',
                ])
                ->unique(ignoreRecord: true)
        );
    }

    /**
     * Отпремање слике која се аутоматски чува као .webp.
     */
    public static function image(string $name, string $directory, string $label = 'Слика'): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
            ->maxSize(10 * 1024)
            ->imagePreviewHeight('200')
            ->helperText('JPG, PNG или WebP до 10 MB. Слика се чува у WebP формату.')
            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => WebpImage::store($file, $directory));
    }
}
