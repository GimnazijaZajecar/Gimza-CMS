<?php

namespace App\Filament\Resources\Exams\Tables;

use App\Enums\ExamType;
use App\Models\Exam;
use App\Models\SchoolClass;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['schoolClass', 'subject', 'professor']))
            ->defaultSort('date')
            ->emptyStateHeading('Нема заказаних провера')
            ->emptyStateDescription('Кликните „Закажи проверу“ да додате контролни или писмени задатак.')
            ->columns([
                TextColumn::make('date')
                    ->label('Датум')
                    ->sortable()
                    ->formatStateUsing(fn (Exam $record): string => $record->date->locale('sr_Cyrl')->isoFormat('dd, DD.MM.YYYY.')),
                TextColumn::make('schoolClass.name')
                    ->label('Одељење')
                    ->weight(FontWeight::Bold),
                TextColumn::make('subject.name')
                    ->label('Предмет')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Врста')
                    ->badge(),
                TextColumn::make('period')
                    ->label('Час')
                    ->formatStateUsing(fn (?int $state): string => $state ? "{$state}." : '—')
                    ->placeholder('—'),
                TextColumn::make('professor.name')
                    ->label('Професор')
                    ->placeholder('—'),
                TextColumn::make('note')
                    ->label('Напомена')
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('upcoming')
                    ->label('Само предстојеће')
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', '>=', today()))
                    ->default(),
                Filter::make('mine')
                    ->label('Само моје провере')
                    ->query(fn (Builder $query): Builder => $query->where('user_id', auth()->id())),
                SelectFilter::make('school_class_id')
                    ->label('Одељење')
                    ->options(fn (): array => SchoolClass::options()),
                SelectFilter::make('type')
                    ->label('Врста')
                    ->options(ExamType::class),
                SelectFilter::make('subject_id')
                    ->label('Предмет')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()->iconButton()->tooltip('Уреди'),
                DeleteAction::make()->iconButton()->tooltip('Обриши'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
