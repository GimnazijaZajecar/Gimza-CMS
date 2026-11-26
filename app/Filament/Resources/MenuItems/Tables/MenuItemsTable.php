<?php

namespace App\Filament\Resources\MenuItems\Tables;

use App\Enums\MenuLocation;
use App\Models\MenuItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['parent', 'page']))
            // Подставке стоје одмах испод своје надкатегорије.
            ->defaultSort(fn (Builder $query): Builder => $query->orderByRaw(
                'COALESCE((SELECT p."order" FROM menu_items AS p WHERE p.id = menu_items.parent_id), menu_items."order"), COALESCE(menu_items.parent_id, menu_items.id), menu_items.parent_id IS NOT NULL, menu_items."order"'
            ))
            ->groups([
                Group::make('menu')
                    ->label('Мени')
                    ->getTitleFromRecordUsing(fn (MenuItem $record): string => $record->menu->getLabel()),
            ])
            ->defaultGroup('menu')
            ->groupingSettingsHidden()
            ->emptyStateHeading('Мени је празан')
            ->columns([
                TextColumn::make('label')
                    ->label('Назив')
                    ->searchable()
                    ->formatStateUsing(fn (string $state, MenuItem $record): string => $record->parent_id ? "↳ {$state}" : $state)
                    ->weight(fn (MenuItem $record): FontWeight => $record->parent_id ? FontWeight::Normal : FontWeight::Bold),
                TextColumn::make('href')
                    ->label('Води на')
                    ->description(fn (MenuItem $record): ?string => $record->page ? 'Страница: '.$record->page->title : null),
                TextColumn::make('parent.label')
                    ->label('Надкатегорија')
                    ->placeholder('—'),
                TextColumn::make('order')
                    ->label('Редослед'),
                IconColumn::make('is_active')
                    ->label('Видљиво')
                    ->boolean(),
                IconColumn::make('is_external')
                    ->label('Нови прозор')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('menu')
                    ->label('Мени')
                    ->options(MenuLocation::class),
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
