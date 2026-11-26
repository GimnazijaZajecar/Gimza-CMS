<?php

namespace App\Filament\Resources\Subjects\Schemas;

use App\Filament\Support\Fields;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Fields::validateOnBlur(
                            TextInput::make('name')
                                ->label('Назив предмета')
                                ->required()
                                ->maxLength(100)
                                ->unique(ignoreRecord: true)
                        ),
                        Select::make('staff')
                            ->label('Професори који предају предмет')
                            ->relationship('staff', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}
