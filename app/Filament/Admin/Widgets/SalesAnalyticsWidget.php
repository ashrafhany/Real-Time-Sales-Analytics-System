<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales Analytics';

    // Enable real-time updates
    protected static bool $isLazy = false;
    protected static ?string $pollingInterval = '5s';

    protected int $filterDays = 30;

    protected function getFilters(): ?array
    {
        return [
            7 => '7 days',
            30 => '30 days',
            60 => '60 days',
            90 => '90 days',
            365 => '1 year',
        ];
    }

    protected function getData(): array
    {
        $days = $this->filterDays;

        // Get sales data grouped by date
        $salesData = Order::select(
                DB::raw('DATE(order_date) as date'),
                DB::raw('SUM(price * quantity) as total')
            )
            ->where('order_date', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Format dates for labels without relying on intl extension
        $labels = $salesData->pluck('date')->map(function ($date) {
            $parsed = Carbon::parse($date);
            return $parsed->format('m/d'); // Simple month/day format without intl
        })->toArray();

        // Format sales totals for dataset
        $totals = $salesData->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Sales by Day',
                    'data' => $totals,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'borderWidth' => 1
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
