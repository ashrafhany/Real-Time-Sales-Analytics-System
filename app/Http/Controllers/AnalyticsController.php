<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Get real-time sales analytics
     */
    public function index(): JsonResponse
    {
        try {
            $now = Carbon::now();
            $oneMinuteAgo = $now->copy()->subMinute();

            // Total revenue from all orders
            $totalRevenue = Order::sum(DB::raw('quantity * price'));

            // Top products by sales (total revenue per product)
            $topProducts = Order::select(
                'product_id',
                'products.name as product_name',
                DB::raw('SUM(orders.quantity * orders.price) as total_revenue'),
                DB::raw('SUM(orders.quantity) as total_quantity')
            )
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->groupBy('product_id', 'products.name')
            ->orderBy('total_revenue', 'desc')
            ->limit(10)
            ->get();

            // Revenue changes in the last 1 minute
            $recentRevenue = Order::where('order_date', '>=', $oneMinuteAgo)
                ->sum(DB::raw('quantity * price'));

            // Count of orders in the last 1 minute
            $recentOrdersCount = Order::where('order_date', '>=', $oneMinuteAgo)->count();

            // Additional insights for better analytics
            $previousMinuteStart = $oneMinuteAgo->copy()->subMinute();
            $previousMinuteRevenue = Order::whereBetween('order_date', [$previousMinuteStart, $oneMinuteAgo])
                ->sum(DB::raw('quantity * price'));

            $revenueChange = $recentRevenue - $previousMinuteRevenue;
            $revenueChangePercentage = $previousMinuteRevenue > 0
                ? round(($revenueChange / $previousMinuteRevenue) * 100, 2)
                : ($recentRevenue > 0 ? 100 : 0);

            return response()->json([
                'success' => true,
                'data' => [
                    'total_revenue' => round($totalRevenue, 2),
                    'top_products' => $topProducts->map(function ($product) {
                        return [
                            'product_id' => $product->product_id,
                            'product_name' => $product->product_name,
                            'total_revenue' => round($product->total_revenue, 2),
                            'total_quantity_sold' => $product->total_quantity,
                        ];
                    }),
                    'last_minute_analytics' => [
                        'revenue_change' => round($recentRevenue, 2),
                        'orders_count' => $recentOrdersCount,
                        'revenue_change_from_previous_minute' => round($revenueChange, 2),
                        'revenue_change_percentage' => $revenueChangePercentage,
                        'time_range' => [
                            'from' => $oneMinuteAgo->toISOString(),
                            'to' => $now->toISOString(),
                        ]
                    ],
                    'timestamp' => $now->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
