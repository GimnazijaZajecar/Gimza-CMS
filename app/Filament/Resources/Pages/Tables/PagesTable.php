<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Models\Page;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('parent')->withCount('articles'))
            ->defaultSort(fn (Builder $query): Builder => $query->orderByRaw(
                'COALESCE((SELECT p."order" FROM pages AS p WHERE p.id = pages.parent_id), pages."order"), COALESCE(pages.parent_id, pages.id), pages.parent_id IS NOT NULL, pages."order", pages.title'
            ))
            ->emptyStateHeading('Још нема страница')
            ->columns([
                TextColumn::make('title')
                    ->label('Наслов')
                    ->searchable()
                    ->formatStateUsing(fn (string $state, Page $record): string => $record->parent_id ? "↳ {$state}" : $state),
                TextColumn::make('parent.title')
                    ->label('Надстраница')
                    ->placeholder('— главна'),
                TextColumn::make('slug')
                    ->label('URL адреса')
                    ->formatStateUsing(fn (string $state): string => "/{$state}"),
                TextColumn::make('articles_count')
                    ->label('Чланака'),
                TextColumn::make('order')
                    ->label('Редослед')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Видљиво')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Измењено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
