<?php

require __DIR__ . '/vendor/autoload.php';

use App\Events\NewOrderCreated;
use App\Models\Order;
use App\Models\Product;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Broadcasting Debug Script ===\n";

// Check broadcasting driver
echo "Broadcasting driver: " . config('broadcasting.default') . "\n";
echo "Reverb configuration:\n";
print_r(config('broadcasting.connections.reverb'));
echo "\n";

// Check if we can find a product
$product = Product::first();
if (!$product) {
    echo "No products found. Run: php artisan db:seed --class=ProductSeeder\n";
    exit(1);
}

echo "Found product: {$product->name}\n";

// Create a test order
echo "Creating test order...\n";
$order = Order::create([
    'product_id' => $product->id,
    'quantity' => 1,
    'price' => 99.99,
    'order_date' => now(),
]);

echo "Order created with ID: {$order->id}\n";

// Load the product relationship
$order->load('product');

// Broadcast the event
echo "Broadcasting NewOrderCreated event...\n";
try {
    $event = new NewOrderCreated($order);

    // Check event configuration
    echo "Event channels: " . json_encode($event->broadcastOn()) . "\n";
    echo "Event name: " . $event->broadcastAs() . "\n";
    echo "Event data: " . json_encode($event->broadcastWith()) . "\n";

    // Try to broadcast
    $result = broadcast($event);
    echo "✓ Event broadcasted successfully\n";
    echo "Broadcast result: " . json_encode($result) . "\n";

    // Also try broadcasting with explicit driver
    echo "Broadcasting with explicit reverb driver...\n";
    broadcast($event)->via('reverb');
    echo "✓ Explicit reverb broadcast completed\n";

} catch (Exception $e) {
    echo "✗ Broadcasting failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== Debug Complete ===\n";
