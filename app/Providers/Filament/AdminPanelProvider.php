<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditAccount;
use App\Filament\Support\InitialsAvatarProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->profile(EditAccount::class, isSimple: false)
            ->brandName('Гимназија Зајечар')
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->defaultAvatarProvider(InitialsAvatarProvider::class)
            // Српски текст у пољу за отпремање фајлова.
            ->renderHook(PanelsRenderHook::BODY_END, fn (): View => view('filament.filepond-sr'))
            // После „Направи“ и „Сачувај“ враћа се на листу.
            ->resourceCreatePageRedirect('index')
            ->resourceEditPageRedirect('index')
            ->unsavedChangesAlerts()
            ->navigationGroups([
                'Садржај сајта',
                'Структура сајта',
                'Школа',
                'Налог',
            ])
            ->navigationItems([
                NavigationItem::make('Подешавања налога')
                    ->group('Налог')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->url(fn (): string => filament()->getProfileUrl())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.auth.profile')),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
