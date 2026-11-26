<?php

namespace App\Providers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Датуми у српском формату у целом панелу (нпр. 26.11.2026. 20:15).
        Table::configureUsing(fn (Table $table) => $table
            ->defaultDateDisplayFormat('d.m.Y.')
            ->defaultDateTimeDisplayFormat('d.m.Y. H:i')
            ->defaultTimeDisplayFormat('H:i'));

        Schema::configureUsing(fn (Schema $schema) => $schema
            ->defaultDateDisplayFormat('d.m.Y.')
            ->defaultDateTimeDisplayFormat('d.m.Y. H:i')
            ->defaultTimeDisplayFormat('H:i'));

        DateTimePicker::configureUsing(fn (DateTimePicker $picker) => $picker
            ->native(false)
            ->firstDayOfWeek(1)
            ->defaultDateDisplayFormat('d.m.Y.')
            ->defaultDateTimeDisplayFormat('d.m.Y. H:i'));
    }
}
