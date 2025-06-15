<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RecommendationController extends Controller
{
    /**
     * Get AI-powered product recommendations based on sales data and weather
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Get recent sales data
            $salesData = $this->getRecentSalesData();

            // Get weather data
            $weatherData = $this->getWeatherData();

            // Generate AI recommendations
            $aiRecommendations = $this->generateAIRecommendations($salesData, $weatherData);

            // Get weather-based product suggestions
            $weatherRecommendations = $this->getWeatherBasedRecommendations($weatherData);

            // Get dynamic pricing suggestions
            $pricingRecommendations = $this->getDynamicPricingSuggestions($salesData, $weatherData);

            return response()->json([
                'success' => true,
                'data' => [
                    'sales_analysis' => $salesData,
                    'weather_info' => $weatherData,
                    'ai_recommendations' => $aiRecommendations,
                    'weather_based_suggestions' => $weatherRecommendations,
                    'pricing_recommendations' => $pricingRecommendations,
                    'strategic_actions' => $this->getStrategicActions($salesData, $weatherData),
                    'timestamp' => now()->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Recommendations API error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent sales data for AI analysis
     */
    private function getRecentSalesData(): array
    {
        $now = Carbon::now();
        $lastWeek = $now->copy()->subWeek();
        $lastMonth = $now->copy()->subMonth();

        // Get recent orders with product information
        $recentOrders = Order::with('product')
            ->where('order_date', '>=', $lastWeek)
            ->get();

        // Calculate metrics
        $totalRevenue = $recentOrders->sum(fn($order) => $order->quantity * $order->price);
        $totalOrders = $recentOrders->count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Top performing products
        $topProducts = $recentOrders->groupBy('product_id')
            ->map(function ($orders) {
                $product = $orders->first()->product;
                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'total_quantity' => $orders->sum('quantity'),
                    'total_revenue' => $orders->sum(fn($order) => $order->quantity * $order->price),
                    'order_count' => $orders->count(),
                    'avg_price' => $orders->avg('price'),
                ];
            })
            ->sortByDesc('total_revenue')
            ->values()
            ->take(5);

        // Low performing products
        $allProducts = Product::all();
        $lowPerformingProducts = $allProducts->filter(function ($product) use ($recentOrders) {
            return $recentOrders->where('product_id', $product->id)->isEmpty();
        })->map(function ($product) {
            return [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category' => $this->getProductCategory($product->name),
                'base_price' => $product->price,
            ];
        })->values();

        // Time-based analysis
        $hourlyData = $recentOrders->groupBy(function ($order) {
            return $order->order_date->format('H');
        })->map(function ($orders, $hour) {
            return [
                'hour' => $hour,
                'order_count' => $orders->count(),
                'revenue' => $orders->sum(fn($order) => $order->quantity * $order->price),
            ];
        })->sortBy('hour')->values();

        return [
            'period' => [
                'start' => $lastWeek->toISOString(),
                'end' => $now->toISOString(),
            ],
            'summary' => [
                'total_revenue' => round($totalRevenue, 2),
                'total_orders' => $totalOrders,
                'avg_order_value' => round($avgOrderValue, 2),
            ],
            'top_products' => $topProducts,
            'low_performing_products' => $lowPerformingProducts,
            'hourly_trends' => $hourlyData,
        ];
    }

    /**
     * Get weather data from OpenWeather API
     */
    private function getWeatherData(): array
    {
        try {
            $apiKey = env('OPENWEATHER_API_KEY', 'demo_key');
            $city = env('WEATHER_CITY', 'London');

            // If no API key is configured, return mock data
            if ($apiKey === 'demo_key') {
                return $this->getMockWeatherData();
            }

            $response = Http::timeout(10)->get("https://api.openweathermap.org/data/2.5/weather", [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'temperature' => $data['main']['temp'],
                    'feels_like' => $data['main']['feels_like'],
                    'humidity' => $data['main']['humidity'],
                    'description' => $data['weather'][0]['description'],
                    'main' => $data['weather'][0]['main'],
                    'wind_speed' => $data['wind']['speed'] ?? 0,
                    'city' => $data['name'],
                    'country' => $data['sys']['country'],
                    'season' => $this->getCurrentSeason(),
                    'source' => 'openweather',
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Weather API failed: ' . $e->getMessage());
        }

        // Fallback to mock data
        return $this->getMockWeatherData();
    }

    /**
     * Generate mock weather data when API is unavailable
     */
    private function getMockWeatherData(): array
    {
        $temperatures = [18, 22, 25, 28, 15, 12, 30, 8, 35];
        $descriptions = ['sunny', 'partly cloudy', 'rainy', 'cloudy', 'clear'];

        return [
            'temperature' => $temperatures[array_rand($temperatures)],
            'feels_like' => $temperatures[array_rand($temperatures)],
            'humidity' => rand(40, 80),
            'description' => $descriptions[array_rand($descriptions)],
            'main' => 'Clear',
            'wind_speed' => rand(5, 15),
            'city' => 'Demo City',
            'country' => 'XX',
            'season' => $this->getCurrentSeason(),
            'source' => 'mock',
        ];
    }

    /**
     * Generate AI-powered recommendations
     */
    private function generateAIRecommendations(array $salesData, array $weatherData): array
    {
        $recommendations = [];

        // Analyze top products
        if (!empty($salesData['top_products'])) {
            $topProduct = $salesData['top_products'][0];
            $recommendations[] = [
                'type' => 'promote_bestseller',
                'priority' => 'high',
                'action' => "Increase marketing budget for '{$topProduct['product_name']}' as it's generating the highest revenue",
                'expected_impact' => 'Revenue increase of 15-25%',
                'reasoning' => 'Top-performing product with proven market demand',
            ];
        }

        // Analyze low-performing products
        foreach ($salesData['low_performing_products'] as $product) {
            $recommendations[] = [
                'type' => 'boost_underperformer',
                'priority' => 'medium',
                'action' => "Create targeted promotion for '{$product['product_name']}' with 20% discount",
                'expected_impact' => 'Increase sales by 40-60%',
                'reasoning' => 'Product has potential but needs promotional push',
            ];
        }

        // Time-based recommendations
        if (!empty($salesData['hourly_trends'])) {
            $peakHour = collect($salesData['hourly_trends'])->sortByDesc('revenue')->first();
            if ($peakHour) {
                $recommendations[] = [
                    'type' => 'timing_optimization',
                    'priority' => 'medium',
                    'action' => "Schedule flash sales during peak hour ({$peakHour['hour']}:00)",
                    'expected_impact' => 'Maximize conversion during high-traffic periods',
                    'reasoning' => 'Historical data shows highest revenue at this time',
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Get weather-based product recommendations
     */
    private function getWeatherBasedRecommendations(array $weatherData): array
    {
        $recommendations = [];
        $temperature = $weatherData['temperature'];
        $description = strtolower($weatherData['description']);

        // Temperature-based recommendations
        if ($temperature > 25) {
            $recommendations[] = [
                'trigger' => 'hot_weather',
                'temperature' => $temperature,
                'products' => ['Bluetooth Speaker', 'Tablet Air'],
                'action' => 'Promote portable electronics for outdoor activities',
                'discount_suggestion' => '15% off summer electronics bundle',
                'reasoning' => 'Hot weather increases outdoor activity and electronics usage',
            ];
        } elseif ($temperature < 10) {
            $recommendations[] = [
                'trigger' => 'cold_weather',
                'temperature' => $temperature,
                'products' => ['Laptop Pro 15"', 'Mechanical Keyboard'],
                'action' => 'Promote indoor electronics and productivity tools',
                'discount_suggestion' => '20% off work-from-home bundle',
                'reasoning' => 'Cold weather encourages indoor activities and productivity',
            ];
        } else {
            $recommendations[] = [
                'trigger' => 'moderate_weather',
                'temperature' => $temperature,
                'products' => ['Wireless Headphones', 'Smartphone X'],
                'action' => 'Promote general-use electronics',
                'discount_suggestion' => '10% off communication devices',
                'reasoning' => 'Mild weather maintains normal consumption patterns',
            ];
        }

        // Weather condition-based recommendations
        if (str_contains($description, 'rain')) {
            $recommendations[] = [
                'trigger' => 'rainy_weather',
                'condition' => $description,
                'products' => ['Gaming Mouse', 'Mechanical Keyboard'],
                'action' => 'Promote indoor entertainment and gaming products',
                'discount_suggestion' => '25% off gaming accessories',
                'reasoning' => 'Rainy weather increases indoor entertainment consumption',
            ];
        }

        return $recommendations;
    }

    /**
     * Get dynamic pricing suggestions
     */
    private function getDynamicPricingSuggestions(array $salesData, array $weatherData): array
    {
        $suggestions = [];
        $temperature = $weatherData['temperature'];
        $season = $weatherData['season'];

        // Seasonal pricing adjustments
        $seasonalMultipliers = [
            'spring' => 1.0,
            'summer' => 1.1,
            'autumn' => 0.95,
            'winter' => 0.9,
        ];

        // Weather-based pricing
        $weatherMultiplier = 1.0;
        if ($temperature > 30) {
            $weatherMultiplier = 1.15; // Premium pricing for hot weather
        } elseif ($temperature < 5) {
            $weatherMultiplier = 0.85; // Discount pricing for very cold weather
        }

        // Apply to products
        $products = Product::all();
        foreach ($products as $product) {
            $category = $this->getProductCategory($product->name);
            $basePrice = $product->price;

            $seasonalPrice = $basePrice * ($seasonalMultipliers[$season] ?? 1.0);
            $weatherAdjustedPrice = $seasonalPrice * $weatherMultiplier;

            // Demand-based adjustment (from sales data)
            $demandMultiplier = $this->getDemandMultiplier($product->id, $salesData);
            $finalPrice = $weatherAdjustedPrice * $demandMultiplier;

            $suggestions[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category' => $category,
                'current_price' => $basePrice,
                'suggested_price' => round($finalPrice, 2),
                'price_change_percent' => round((($finalPrice - $basePrice) / $basePrice) * 100, 1),
                'factors' => [
                    'season' => $season,
                    'weather' => $weatherData['description'],
                    'temperature_impact' => round(($weatherMultiplier - 1) * 100, 1) . '%',
                    'demand_impact' => round(($demandMultiplier - 1) * 100, 1) . '%',
                ],
            ];
        }

        return $suggestions;
    }

    /**
     * Get strategic actions based on comprehensive analysis
     */
    private function getStrategicActions(array $salesData, array $weatherData): array
    {
        $actions = [];

        // Revenue optimization
        if ($salesData['summary']['avg_order_value'] < 100) {
            $actions[] = [
                'category' => 'revenue_optimization',
                'priority' => 'high',
                'action' => 'Implement bundle offers to increase average order value',
                'target' => 'Increase AOV from $' . round($salesData['summary']['avg_order_value'], 2) . ' to $120',
                'timeline' => '2 weeks',
            ];
        }

        // Inventory management
        $actions[] = [
            'category' => 'inventory_management',
            'priority' => 'medium',
            'action' => 'Adjust inventory levels based on weather forecasts',
            'target' => 'Optimize stock for ' . $weatherData['season'] . ' season',
            'timeline' => '1 week',
        ];

        // Marketing focus
        if (!empty($salesData['low_performing_products'])) {
            $actions[] = [
                'category' => 'marketing_focus',
                'priority' => 'medium',
                'action' => 'Launch targeted campaigns for underperforming products',
                'target' => 'Increase sales for ' . count($salesData['low_performing_products']) . ' products',
                'timeline' => '3 weeks',
            ];
        }

        // Weather-responsive strategy
        $actions[] = [
            'category' => 'weather_responsive',
            'priority' => 'low',
            'action' => 'Implement automated weather-based promotions',
            'target' => 'Increase weather-sensitive product sales by 30%',
            'timeline' => '1 month',
        ];

        return $actions;
    }

    /**
     * Get product category based on name
     */
    private function getProductCategory(string $productName): string
    {
        $categories = [
            'laptop' => 'computers',
            'headphones' => 'audio',
            'smartphone' => 'mobile',
            'mouse' => 'accessories',
            'keyboard' => 'accessories',
            'monitor' => 'displays',
            'speaker' => 'audio',
            'tablet' => 'mobile',
        ];

        $productLower = strtolower($productName);
        foreach ($categories as $keyword => $category) {
            if (str_contains($productLower, $keyword)) {
                return $category;
            }
        }

        return 'general';
    }

    /**
     * Get current season
     */
    private function getCurrentSeason(): string
    {
        $month = now()->month;

        if (in_array($month, [12, 1, 2])) return 'winter';
        if (in_array($month, [3, 4, 5])) return 'spring';
        if (in_array($month, [6, 7, 8])) return 'summer';
        return 'autumn';
    }

    /**
     * Calculate demand multiplier based on recent sales
     */
    private function getDemandMultiplier(int $productId, array $salesData): float
    {
        $productSales = collect($salesData['top_products'])
            ->firstWhere('product_id', $productId);

        if (!$productSales) {
            return 0.9; // Lower price for low-demand products
        }

        $totalRevenue = $salesData['summary']['total_revenue'];
        $productRevenue = $productSales['total_revenue'];

        if ($totalRevenue > 0) {
            $marketShare = $productRevenue / $totalRevenue;

            if ($marketShare > 0.3) return 1.1; // High demand
            if ($marketShare > 0.1) return 1.05; // Medium demand
        }

        return 1.0; // Normal demand
    }
}
