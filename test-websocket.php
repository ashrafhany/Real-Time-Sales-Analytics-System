<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Events\NewOrderCreated;
use App\Events\AnalyticsUpdated;
use App\Models\Order;
use App\Models\Product;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🧪 Testing WebSocket Broadcasting\n";
echo "================================\n\n";

try {
    // Test 1: Create a sample order
    echo "📦 Creating sample order...\n";

    $product = Product::first();
    if (!$product) {
        echo "❌ No products found. Please run: php artisan migrate:fresh --seed\n";
        exit(1);
    }

    $order = Order::create([
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => $product->price,
        'order_date' => now(),
    ]);

    $order->load('product');
    echo "✅ Order created: #{$order->id} - {$order->quantity}x {$order->product->name}\n";

    // Test 2: Broadcast new order event
    echo "📡 Broadcasting new order event...\n";
    broadcast(new NewOrderCreated($order));
    echo "✅ New order event broadcasted\n";

    // Test 3: Broadcast analytics update
    echo "📊 Broadcasting analytics update...\n";
    $sampleAnalytics = [
        'total_revenue' => 12345.67,
        'top_products' => [
            [
                'product_id' => 1,
                'product_name' => 'Test Product',
                'total_revenue' => 1000.00,
                'total_quantity_sold' => 5
            ]
        ],
        'last_minute_analytics' => [
            'revenue_change' => 199.99,
            'orders_count' => 1,
            'revenue_change_from_previous_minute' => 199.99,
            'revenue_change_percentage' => 100,
            'time_range' => [
                'from' => now()->subMinute()->toISOString(),
                'to' => now()->toISOString(),
            ]
        ],
        'timestamp' => now()->toISOString(),
    ];

    broadcast(new AnalyticsUpdated($sampleAnalytics));
    echo "✅ Analytics update event broadcasted\n\n";

    echo "🎉 All broadcasting tests completed!\n";
    echo "💡 Open the API tester at: http://localhost:8000/api-tester.html\n";
    echo "🔗 Connect to WebSocket and you should see the events in real-time\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
