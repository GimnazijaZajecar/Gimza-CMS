<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Filament\Support\AdminOnly;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Налози администратора и професора. Професори се пријављују да би заказали провере.
 */
class UserResource extends Resource
{
    use AdminOnly {
        AdminOnly::getAuthorizationResponse as adminOnlyAuthorizationResponse;
    }

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Школа';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Корисници';

    protected static ?string $modelLabel = 'корисник';

    protected static ?string $pluralModelLabel = 'корисници';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        // Администратор не може да обрише свој налог.
        if ($action === 'delete' && $record?->is(auth()->user())) {
            return Response::deny('Не можете обрисати свој налог.');
        }

        return static::adminOnlyAuthorizationResponse($action, $record);
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
