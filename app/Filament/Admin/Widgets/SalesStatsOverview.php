<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesStatsOverview extends BaseWidget
{
    // Enable real-time updates
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {
        // Calculate today's sales
        $todaySales = Order::whereDate('order_date', Carbon::today())
            ->sum(\DB::raw('price * quantity'));

        // Calculate yesterday's sales for comparison
        $yesterdaySales = Order::whereDate('order_date', Carbon::yesterday())
            ->sum(\DB::raw('price * quantity'));

        $salesDifference = $todaySales - $yesterdaySales;
        $salesPercentage = $yesterdaySales > 0
            ? round(($salesDifference / $yesterdaySales) * 100, 1)
            : 100;

        // Get order count
        $orderCount = Order::whereDate('order_date', Carbon::today())->count();
        $yesterdayOrderCount = Order::whereDate('order_date', Carbon::yesterday())->count();
        $orderDifference = $orderCount - $yesterdayOrderCount;

        // Get top selling product
        $topProduct = Product::select('products.name', \DB::raw('SUM(orders.quantity) as total_quantity'))
            ->join('orders', 'products.id', '=', 'orders.product_id')
            ->whereDate('orders.order_date', '>=', Carbon::now()->subDays(7))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->first();

        return [
            Stat::make('Today\'s Sales', '$' . sprintf('%.2f', $todaySales))
                ->description($salesPercentage >= 0 ? sprintf("%.1f", $salesPercentage) . '% increase' : sprintf("%.1f", abs($salesPercentage)) . '% decrease')
                ->descriptionIcon($salesPercentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($salesPercentage >= 0 ? 'success' : 'danger')
                ->chart([
                    $yesterdaySales / 100,
                    $todaySales / 100,
                ]),

            Stat::make('Today\'s Orders', $orderCount)
                ->description($orderDifference >= 0 ? "{$orderDifference} more than yesterday" : abs($orderDifference) . ' less than yesterday')
                ->descriptionIcon($orderDifference >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($orderDifference >= 0 ? 'success' : 'danger')
                ->chart([
                    $yesterdayOrderCount,
                    $orderCount,
                ]),

            Stat::make('Top Product (7 days)', $topProduct ? $topProduct->name : 'N/A')
                ->description($topProduct ? sprintf("%d units sold", $topProduct->total_quantity) : 'No sales data')
                ->color('info'),
        ];
    }
}
