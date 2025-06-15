<?php

namespace App\Http\Controllers;

use App\Events\AnalyticsUpdated;
use App\Events\NewOrderCreated;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Store a new order
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'date' => 'nullable|date',
            ]);

            // Use provided date or current timestamp
            $orderDate = $validated['date'] ?? now();

            // Verify product exists and get product info
            $product = Product::findOrFail($validated['product_id']);

            // Create the order
            $order = Order::create([
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'price' => $validated['price'],
                'order_date' => $orderDate,
            ]);

            // Load the product relationship
            $order->load('product');

            // Broadcast new order event
            broadcast(new NewOrderCreated($order));

            // Calculate and broadcast updated analytics
            $this->broadcastAnalyticsUpdate();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => [
                    'id' => $order->id,
                    'product_id' => $order->product_id,
                    'product_name' => $order->product->name,
                    'quantity' => $order->quantity,
                    'price' => $order->price,
                    'total' => $order->total,
                    'order_date' => $order->order_date->toISOString(),
                    'created_at' => $order->created_at->toISOString(),
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Broadcast updated analytics data
     */
    private function broadcastAnalyticsUpdate(): void
    {
        try {
            $now = now();
            $oneMinuteAgo = $now->copy()->subMinute();

            // Calculate analytics data (same logic as AnalyticsController)
            $totalRevenue = Order::sum(DB::raw('quantity * price'));

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

            $recentRevenue = Order::where('order_date', '>=', $oneMinuteAgo)
                ->sum(DB::raw('quantity * price'));

            $recentOrdersCount = Order::where('order_date', '>=', $oneMinuteAgo)->count();

            $previousMinuteStart = $oneMinuteAgo->copy()->subMinute();
            $previousMinuteRevenue = Order::whereBetween('order_date', [$previousMinuteStart, $oneMinuteAgo])
                ->sum(DB::raw('quantity * price'));

            $revenueChange = $recentRevenue - $previousMinuteRevenue;
            $revenueChangePercentage = $previousMinuteRevenue > 0
                ? round(($revenueChange / $previousMinuteRevenue) * 100, 2)
                : ($recentRevenue > 0 ? 100 : 0);

            $analyticsData = [
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
            ];

            broadcast(new AnalyticsUpdated($analyticsData));
        } catch (\Exception $e) {
            // Log error but don't fail the order creation
            \Log::error('Failed to broadcast analytics update: ' . $e->getMessage());
        }
    }
}
