<?php

namespace App\Filament\Resources\SchoolClasses\Schemas;

use App\Models\SchoolClass;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SchoolClassForm
{
    public const PROGRAMS = [
        'Општи тип',
        'Природно-математички смер',
        'Друштвено-језички смер',
        'Одељење за ученике са посебним способностима за рачунарство и информатику',
    ];

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        ToggleButtons::make('grade')
                            ->label('Разред')
                            ->options(SchoolClass::GRADES)
                            ->inline()
                            ->required(),
                        TextInput::make('section')
                            ->label('Број одељења')
                            ->helperText('Нпр. 2 за одељење III-2.')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(15)
                            ->required()
                            ->rule(fn (Get $get, ?SchoolClass $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                                $exists = SchoolClass::query()
                                    ->where('grade', $get('grade'))
                                    ->where('section', $value)
                                    ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                    ->exists();

                                if ($exists) {
                                    $fail('Ово одељење већ постоји.');
                                }
                            }),
                        TextInput::make('program')
                            ->label('Смер')
                            ->datalist(self::PROGRAMS)
                            ->maxLength(120)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
