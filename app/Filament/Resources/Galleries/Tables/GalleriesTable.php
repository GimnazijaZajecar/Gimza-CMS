<?php

namespace App\Filament\Resources\Galleries\Tables;

use App\Models\GalleryImage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GalleriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with('creator')
                ->withCount('images')
                ->addSelect(['cover_path' => GalleryImage::query()
                    ->select('path')
                    ->whereColumn('gallery_id', 'galleries.id')
                    ->orderBy('order')
                    ->limit(1)]))
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Још нема подгалерија')
            ->emptyStateDescription('Направите прву подгалерију, нпр. „Дан школе 2026“, и додајте слике.')
            ->columns([
                ImageColumn::make('cover_path')
                    ->label('Насловна')
                    ->disk('public')
                    ->imageHeight(48),
                TextColumn::make('title')
                    ->label('Подгалерија')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): ?string => $record->description ? str($record->description)->limit(45)->toString() : null),
                TextColumn::make('images_count')
                    ->label('Број слика')
                    ->sortable(),
                TextColumn::make('album_url')
                    ->label('Спољни албум')
                    ->formatStateUsing(fn (): string => 'Отвори')
                    ->url(fn ($record): ?string => $record->album_url)
                    ->openUrlInNewTab()
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->color('primary')
                    ->placeholder('—'),
                IconColumn::make('is_active')
                    ->label('Видљиво')
                    ->boolean(),
                TextColumn::make('creator.name')
                    ->label('Направио')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Датум')
                    ->date()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()->iconButton()->tooltip('Уреди'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
