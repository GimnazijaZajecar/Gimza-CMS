<?php

namespace App\Filament\Resources\Tags\Schemas;

use App\Filament\Support\Fields;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Fields::validateOnBlur(
                            TextInput::make('name')
                                ->label('Назив ознаке')
                                ->required()
                                ->maxLength(50)
                                ->unique(ignoreRecord: true)
                        ),
                    ]),
            ]);
    }
}
