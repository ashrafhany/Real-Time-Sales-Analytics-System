<?php

namespace App\Providers;

use Filament\Navigation\UserMenuItem;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            // Configure Filament colors
            FilamentColor::register([
                'primary' => Color::Blue,
                'gray' => Color::Zinc,
            ]);

            // Add custom menu items
            Filament::registerUserMenuItems([
                UserMenuItem::make()
                    ->label('Sales Dashboard')
                    ->url(route('filament.admin.pages.dashboard'))
                    ->icon('heroicon-o-chart-bar'),
            ]);
        });
    }
}
