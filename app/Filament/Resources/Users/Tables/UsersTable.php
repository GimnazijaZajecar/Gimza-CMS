<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\UserRole;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('subjects'))
            ->defaultSort('last_name')
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->imageSize(40),
                TextColumn::make('name')
                    ->label('Име и презиме')
                    ->searchable(['first_name', 'last_name', 'name'])
                    ->sortable(['last_name', 'first_name'])
                    ->description(fn (User $record): ?string => $record->title),
                TextColumn::make('username')
                    ->label('Корисничко име')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Имејл')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Телефон')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role')
                    ->label('Улога')
                    ->badge(),
                TextColumn::make('subjects.name')
                    ->label('Предмети')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->placeholder('—')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Активан')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Улога')
                    ->options(UserRole::class),
                TernaryFilter::make('is_active')
                    ->label('Активан'),
            ])
            ->recordActions([
                EditAction::make()->iconButton()->tooltip('Уреди'),
            ]);
    }
}
