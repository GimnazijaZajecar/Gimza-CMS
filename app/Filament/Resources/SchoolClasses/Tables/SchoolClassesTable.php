<?php

namespace App\Filament\Resources\SchoolClasses\Tables;

use App\Models\SchoolClass;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SchoolClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('exams'))
            ->defaultSort(fn (Builder $query): Builder => $query->orderBy('grade')->orderBy('section'))
            ->emptyStateHeading('Још нема одељења')
            ->columns([
                TextColumn::make('name')
                    ->label('Одељење')
                    ->weight(FontWeight::Bold),
                TextColumn::make('program')
                    ->label('Смер')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('exams_count')
                    ->label('Заказаних провера'),
            ])
            ->filters([
                SelectFilter::make('grade')
                    ->label('Разред')
                    ->options(SchoolClass::GRADES),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
