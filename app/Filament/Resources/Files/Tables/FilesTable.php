<?php

namespace App\Filament\Resources\Files\Tables;

use App\Models\File;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FilesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['uploadedBy', 'updatedBy']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Назив')
                    ->searchable()
                    ->sortable()
                    ->limit(45),
                TextColumn::make('slug')
                    ->label('URL адреса')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('URL адреса је копирана'),
                TextColumn::make('extension')
                    ->label('Врста')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('human_size')
                    ->label('Величина')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('uploadedBy.name')
                    ->label('Отпремио')
                    ->description(fn (File $record): ?string => $record->created_at?->format('d.m.Y. H:i'))
                    ->placeholder('—'),
                TextColumn::make('updatedBy.name')
                    ->label('Изменио')
                    ->description(fn (File $record): ?string => $record->updated_by_id ? $record->updated_at?->format('d.m.Y. H:i') : null)
                    ->placeholder('Није мењано'),
                IconColumn::make('is_active')
                    ->label('Видљиво')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Датум отпремања')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Датум измене')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Видљиво на сајту'),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Преузми')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('gray')
                    ->iconButton()
                    ->tooltip('Преузми')
                    ->url(fn (File $record): ?string => $record->url)
                    ->openUrlInNewTab(),
                EditAction::make()->iconButton()->tooltip('Уреди'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
