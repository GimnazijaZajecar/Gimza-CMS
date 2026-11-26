<?php

namespace App\Filament\Resources\Tags;

use App\Filament\Resources\Tags\Pages\EditTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Filament\Resources\Tags\Schemas\TagForm;
use App\Filament\Resources\Tags\Tables\TagsTable;
use App\Filament\Support\AdminOnly;
use App\Models\Tag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Ознаке се праве из падајуће листе у формулару чланка.
 * Овде се само преименују или бришу.
 */
class TagResource extends Resource
{
    use AdminOnly;

    protected static ?string $model = Tag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Садржај сајта';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Ознаке';

    protected static ?string $modelLabel = 'ознака';

    protected static ?string $pluralModelLabel = 'ознаке';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return TagForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTags::route('/'),
            'edit' => EditTag::route('/{record}/edit'),
        ];
    }
}
