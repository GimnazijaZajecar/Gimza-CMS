<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Support\Fields;
use App\Models\Category;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Fields::title('name', 'Назив'),
                        Fields::slug(),
                        Select::make('parent_id')
                            ->label('Надкатегорија')
                            ->placeholder('Нема – ово је главна категорија')
                            ->options(fn (?Category $record): array => Category::query()
                                ->topLevel()
                                ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->disabled(fn (?Category $record): bool => (bool) $record?->children()->exists())
                            ->helperText(fn (?Category $record): string => $record?->children()->exists()
                                ? 'Ова категорија има поткатегорије, па не може и сама да буде поткатегорија.'
                                : 'Изаберите надкатегорију ако је ово поткатегорија (нпр. Такмичења → Информатика).')
                            ->rule(fn (?Category $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                if (blank($value)) {
                                    return;
                                }

                                $parent = Category::find($value);

                                if (! $parent) {
                                    $fail('Изабрана надкатегорија не постоји.');
                                } elseif ($record && (int) $value === $record->getKey()) {
                                    $fail('Категорија не може бити сама себи надкатегорија.');
                                } elseif ($parent->parent_id !== null) {
                                    $fail('Надкатегорија мора бити главна категорија (дозвољена су два нивоа).');
                                }
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
