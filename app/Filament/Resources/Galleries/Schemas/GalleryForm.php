<?php

namespace App\Filament\Resources\Galleries\Schemas;

use App\Filament\Support\Fields;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Support\WebpImage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class GalleryForm
{
    public const DIRECTORY = 'galerija';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Подгалерија')
                    ->columnSpan(2)
                    ->schema([
                        Fields::title(),
                        Fields::slug(),
                        Textarea::make('description')
                            ->label('Опис')
                            ->rows(3)
                            ->maxLength(1000),
                        TextInput::make('album_url')
                            ->label('Линк ка спољном албуму')
                            ->helperText('Попуните ако су фотографије на спољном албуму (нпр. Google Photos).')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://'),
                    ]),

                Section::make('Објава')
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('is_active')
                            ->label('Видљиво на сајту')
                            ->default(true),
                        TextEntry::make('created_at')
                            ->label('Датум')
                            ->state(fn (?Gallery $record): string => $record?->created_at?->format('d.m.Y.')
                                ?? 'Уписује се сам при чувању.'),
                    ]),

                Section::make('Слике')
                    ->description('Слике се додају превлачењем или кликом. Редослед се мења превлачењем, а свака слика се чува као .webp.')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('images')
                            ->hiddenLabel()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->image()
                            ->disk('public')
                            ->directory(self::DIRECTORY)
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(10 * 1024)
                            ->maxFiles(200)
                            ->panelLayout('grid')
                            ->imagePreviewHeight('160')
                            ->dehydrated(false)
                            ->saveUploadedFileUsing(fn (TemporaryUploadedFile $file): string => WebpImage::store($file, self::DIRECTORY))
                            ->loadStateFromRelationshipsUsing(function (FileUpload $component, ?Gallery $record): void {
                                $component->state($record?->images()->pluck('path')->all() ?? []);
                            })
                            ->saveRelationshipsUsing(function (Gallery $record, ?array $state): void {
                                self::syncImages($record, array_values($state ?? []));
                            }),
                    ]),
            ]);
    }

    /**
     * Усклађује слике подгалерије са стањем у формулару (додате, обрисане, редослед).
     *
     * @param  array<string>  $paths
     */
    public static function syncImages(Gallery $gallery, array $paths): void
    {
        $paths = array_values(array_filter(
            $paths,
            fn ($path): bool => is_string($path)
                && str_starts_with($path, self::DIRECTORY.'/')
                && ! GalleryImage::query()->where('path', $path)->where('gallery_id', '!=', $gallery->getKey())->exists(),
        ));

        $existing = $gallery->images()->get()->keyBy('path');

        foreach ($existing as $path => $image) {
            if (! in_array($path, $paths, true)) {
                $image->delete();
            }
        }

        foreach ($paths as $order => $path) {
            $image = $existing->get($path);

            if ($image) {
                if ($image->order !== $order) {
                    $image->update(['order' => $order]);
                }

                continue;
            }

            $gallery->images()->create(['path' => $path, 'order' => $order]);
        }
    }
}
