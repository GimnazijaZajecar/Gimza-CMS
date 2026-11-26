<?php

namespace App\Filament\Support;

use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Ресурс који види и користи само администратор.
 * Професори виде само распоред провера и подешавања налога.
 */
trait AdminOnly
{
    public static function getAuthorizationResponse(string|UnitEnum $action, ?Model $record = null): Response
    {
        if (! (auth()->user()?->isAdmin() ?? false)) {
            return Response::deny('Ову страницу може да користи само администратор.');
        }

        return parent::getAuthorizationResponse($action, $record);
    }
}
