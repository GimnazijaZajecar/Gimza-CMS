<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('parent')->withCount('articles'))
            // Свака поткатегорија стоји одмах испод своје надкатегорије.
            ->defaultSort(fn (Builder $query): Builder => $query->orderByRaw(
                'COALESCE((SELECT p.name FROM categories AS p WHERE p.id = categories.parent_id), categories.name), categories.parent_id IS NOT NULL, categories.name'
            ))
            ->columns([
                TextColumn::make('name')
                    ->label('Назив')
                    ->searchable()
                    ->formatStateUsing(fn (string $state, Category $record): string => $record->parent_id ? "↳ {$state}" : $state)
                    ->weight(fn (Category $record): FontWeight => $record->parent_id ? FontWeight::Normal : FontWeight::Bold),
                TextColumn::make('parent.name')
                    ->label('Надкатегорија')
                    ->placeholder('— главна категорија'),
                TextColumn::make('articles_count')
                    ->label('Број чланака')
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('URL адреса')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Направљено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Измењено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
