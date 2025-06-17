<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\SalesAnalyticsWidget;
use App\Filament\Admin\Widgets\SalesStatsOverview;
use Filament\Pages\Page;

class SalesDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Sales Dashboard';

    protected static ?string $title = 'Real-Time Sales Dashboard';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.sales-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            SalesStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            SalesAnalyticsWidget::class,
        ];
    }
}
