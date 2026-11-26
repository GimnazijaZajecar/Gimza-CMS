<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Support\Fields;
use App\Models\Article;
use App\Models\Page;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Страница')
                    ->columnSpan(2)
                    ->schema([
                        Fields::title(),
                        Fields::slug(),
                    ]),

                Section::make('Положај на сајту')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('parent_id')
                            ->label('Надстраница')
                            ->placeholder('Нема – главна страница')
                            ->options(fn (?Page $record): array => Page::query()
                                ->whereKeyNot(self::excludedIds($record))
                                ->orderBy('title')
                                ->pluck('title', 'id')
                                ->all())
                            ->searchable()
                            ->rule(fn (?Page $record): Closure => function (string $attribute, mixed $value, Closure $fail) use ($record): void {
                                if (filled($value) && in_array((int) $value, self::excludedIds($record), true)) {
                                    $fail('Страница не може бити испод саме себе или своје подстранице.');
                                }
                            }),
                        TextInput::make('order')
                            ->label('Редослед')
                            ->helperText('Мањи број = раније у листи.')
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(999)
                            ->default(0)
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Видљиво на сајту')
                            ->default(true),
                    ]),

                Section::make('Чланци на страници')
                    ->description('Изаберите чланке који се приказују на овој страници. Редослед мењате превлачењем.')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('pageArticles')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('order')
                            ->reorderable()
                            ->defaultItems(0)
                            ->addActionLabel('Додај чланак')
                            ->simple(
                                Select::make('article_id')
                                    ->label('Чланак')
                                    ->options(fn (): array => Article::query()
                                        ->orderBy('title')
                                        ->get()
                                        ->mapWithKeys(fn (Article $article): array => [
                                            $article->id => "{$article->title} ({$article->type->getLabel()})",
                                        ])
                                        ->all())
                                    ->searchable()
                                    ->required()
                                    ->distinct()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ),
                    ]),
            ]);
    }

    /**
     * ID странице и свих њених подстраница (не смеју бити надстраница).
     *
     * @return array<int>
     */
    public static function excludedIds(?Page $record): array
    {
        if (! $record?->exists) {
            return [];
        }

        $ids = [$record->getKey()];
        $queue = [$record->getKey()];

        while ($queue) {
            $children = Page::query()->whereIn('parent_id', $queue)->pluck('id')->all();
            $queue = array_values(array_diff($children, $ids));
            $ids = array_merge($ids, $queue);
        }

        return $ids;
    }
}
