<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Sales Management APIs
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/analytics', [AnalyticsController::class, 'index']);
Route::get('/recommendations', [RecommendationController::class, 'index']);

// Test routes for WebSocket broadcasting
Route::get('/test-broadcast', function () {
    try {
        // Get the first product from the database
        $product = \App\Models\Product::first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'No products found. Run: php artisan db:seed --class=ProductSeeder'
            ], 404);
        }

        // Create a temporary test order in the database
        $testOrder = \App\Models\Order::create([
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 99.99,
            'order_date' => now(),
        ]);

        // Load the product relationship
        $testOrder->load('product');

        // Broadcast the event
        broadcast(new \App\Events\NewOrderCreated($testOrder));

        // Also broadcast analytics update
        $orderController = new \App\Http\Controllers\OrderController();
        $reflection = new \ReflectionClass($orderController);
        $method = $reflection->getMethod('broadcastAnalyticsUpdate');
        $method->setAccessible(true);
        $method->invoke($orderController);

        return response()->json([
            'success' => true,
            'message' => 'Test broadcast sent successfully!',
            'data' => [
                'id' => $testOrder->id,
                'product_name' => $testOrder->product->name,
                'quantity' => $testOrder->quantity,
                'price' => $testOrder->price,
                'total' => $testOrder->total
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Broadcast failed: ' . $e->getMessage()
        ], 500);
    }
});

Route::post('/test-broadcast', function () {
    try {
        // Get the first product from the database
        $product = \App\Models\Product::first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'No products found. Run: php artisan db:seed --class=ProductSeeder'
            ], 404);
        }

        // Calculate a random quantity between 1 and 5
        $quantity = rand(1, 5);

        // Random price between 50 and 200
        $price = round(rand(5000, 20000) / 100, 2);

        // Create a temporary test order in the database
        $testOrder = \App\Models\Order::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $price,
            'order_date' => now(),
        ]);

        // Load the product relationship
        $testOrder->load('product');

        // Broadcast the event
        event(new \App\Events\NewOrderCreated($testOrder));

        // Also broadcast analytics update
        event(new \App\Events\AnalyticsUpdated([
            'new_order' => [
                'id' => $testOrder->id,
                'product' => $testOrder->product->name,
                'quantity' => $testOrder->quantity,
                'price' => $testOrder->price,
                'total' => $testOrder->quantity * $testOrder->price,
                'date' => now()->toDateTimeString()
            ]
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Test broadcast sent successfully!',
            'data' => [
                'id' => $testOrder->id,
                'product_name' => $testOrder->product->name,
                'quantity' => $testOrder->quantity,
                'price' => $testOrder->price,
                'total' => $testOrder->quantity * $testOrder->price
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Broadcast failed: ' . $e->getMessage()
        ], 500);
    }
});
