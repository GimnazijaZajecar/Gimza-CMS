<?php

namespace App\Filament\Resources\Files\Schemas;

use App\Filament\Support\Fields;
use App\Models\File;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FileForm
{
    /**
     * Дозвољени документи. Слике иду у галерију или у чланак.
     */
    public const ACCEPTED_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'application/vnd.oasis.opendocument.presentation',
        'application/zip',
        'text/plain',
        'text/csv',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Документ')
                    ->columnSpan(2)
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID')
                            ->visibleOn('edit'),
                        Fields::title('title', 'Назив'),
                        Fields::slug()
                            ->helperText('Од ове адресе се прави линк за преузимање на сајту, нпр. /dokumenta/raspored-zvonjenja.'),
                        FileUpload::make('path')
                            ->label('Фајл')
                            ->disk('public')
                            ->directory('dokumenta')
                            ->visibility('public')
                            ->storeFileNamesIn('file_name')
                            ->acceptedFileTypes(self::ACCEPTED_TYPES)
                            ->maxSize(20 * 1024)
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->helperText('PDF, Word, Excel, PowerPoint, OpenDocument, TXT, CSV или ZIP до 20 MB. Врста фајла се препознаје по екстензији.'),
                        Toggle::make('is_active')
                            ->label('Видљиво на сајту')
                            ->default(true),
                    ]),

                Section::make('Евиденција')
                    ->columnSpan(1)
                    ->visibleOn('edit')
                    ->schema([
                        TextEntry::make('uploaded_info')
                            ->label('Отпремио')
                            ->state(fn (File $record): string => ($record->uploadedBy?->full_name ?? 'Непознато').', '.$record->created_at?->format('d.m.Y. H:i')),
                        TextEntry::make('updated_info')
                            ->label('Последњи изменио')
                            ->state(fn (File $record): string => $record->updatedBy
                                ? $record->updatedBy->full_name.', '.$record->updated_at?->format('d.m.Y. H:i')
                                : 'Није мењано'),
                        TextEntry::make('extension')
                            ->label('Врста фајла')
                            ->badge(),
                        TextEntry::make('human_size')
                            ->label('Величина'),
                    ]),
            ]);
    }
}
