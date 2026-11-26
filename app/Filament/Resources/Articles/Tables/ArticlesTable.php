<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Enums\ArticleStatus;
use App\Enums\ArticleType;
use App\Models\Article;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['category.parent', 'creator', 'updater']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image')
                    ->label('Слика')
                    ->disk('public')
                    ->square()
                    ->imageSize(48),
                TextColumn::make('title')
                    ->label('Наслов')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->description(fn (Article $record): ?string => $record->category?->full_name),
                TextColumn::make('type')
                    ->label('Врста')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->sortable(),
                IconColumn::make('is_featured')
                    ->label('Издвојено')
                    ->boolean(),
                TextColumn::make('creator.name')
                    ->label('Аутор'),
                TextColumn::make('published_at')
                    ->label('Објављено')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('event_date')
                    ->label('Датум догађаја')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updater.name')
                    ->label('Изменио')
                    ->placeholder('—')
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
            ->filters([
                SelectFilter::make('type')
                    ->label('Врста')
                    ->options(ArticleType::class),
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(ArticleStatus::class),
                SelectFilter::make('category_id')
                    ->label('Категорија')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_featured')
                    ->label('Издвојено'),
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
