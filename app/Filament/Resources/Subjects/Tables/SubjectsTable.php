<?php

namespace App\Filament\Resources\Subjects\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('staff')->withCount('exams'))
            ->defaultSort('name')
            ->emptyStateHeading('Још нема предмета')
            ->columns([
                TextColumn::make('name')
                    ->label('Предмет')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('staff.name')
                    ->label('Професори')
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->placeholder('—'),
                TextColumn::make('exams_count')
                    ->label('Заказаних провера')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
