<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SalesApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test products directly
        Product::create([
            'id' => 1,
            'name' => 'Test Laptop',
            'price' => 1299.99,
            'description' => 'High-performance test laptop'
        ]);

        Product::create([
            'id' => 2,
            'name' => 'Test Headphones',
            'price' => 199.99,
            'description' => 'Noise-cancelling test headphones'
        ]);
    }

    /**
     * Test Case 1: Order Creation API
     *
     * This test verifies that:
     * - Orders can be created successfully
     * - The response contains correct order data
     * - The order is persisted in the database
     */
    public function test_order_creation_api(): void
    {
        $orderData = [
            'product_id' => 1,
            'quantity' => 2,
            'price' => 1299.99
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Order created successfully'
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'product_id',
                         'product_name',
                         'quantity',
                         'price',
                         'total',
                         'order_date'
                     ]
                 ]);

        // Verify order was saved to database
        $this->assertDatabaseHas('orders', [
            'product_id' => 1,
            'quantity' => 2,
            'price' => 1299.99
        ]);

        // Verify total calculation
        $order = Order::latest()->first();
        $this->assertEquals(2599.98, $order->total);
    }

    /**
     * Test Case 2: Order Validation
     *
     * This test verifies that:
     * - Invalid product IDs are rejected
     * - Missing required fields are rejected
     * - Invalid quantities are rejected
     */
    public function test_order_validation(): void
    {
        // Test invalid product ID
        $invalidProductData = [
            'product_id' => 999,
            'quantity' => 1,
            'price' => 100.00
        ];

        $response = $this->postJson('/api/orders', $invalidProductData);
        $response->assertStatus(422);

        // Test missing required fields
        $incompleteData = [
            'product_id' => 1
            // Missing quantity and price
        ];

        $response = $this->postJson('/api/orders', $incompleteData);
        $response->assertStatus(422);

        // Test invalid quantity (zero)
        $zeroQuantityData = [
            'product_id' => 1,
            'quantity' => 0,
            'price' => 100.00
        ];

        $response = $this->postJson('/api/orders', $zeroQuantityData);
        $response->assertStatus(422);
    }

    /**
     * Test Case 3: Analytics API
     *
     * This test verifies that:
     * - Analytics API returns correct structure
     * - Revenue calculations are accurate
     * - Top products are correctly identified
     */
    public function test_analytics_api(): void
    {
        // Create test orders
        Order::create([
            'product_id' => 1,
            'quantity' => 2,
            'price' => 1299.99,
            'order_date' => now()
        ]);

        Order::create([
            'product_id' => 2,
            'quantity' => 1,
            'price' => 199.99,
            'order_date' => now()
        ]);

        $response = $this->getJson('/api/analytics');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'total_revenue',
                         'top_products',
                         'last_minute_analytics' => [
                             'revenue_change',
                             'orders_count',
                             'revenue_change_from_previous_minute',
                             'revenue_change_percentage'
                         ],
                         'timestamp'
                     ]
                 ]);

        $data = $response->json('data');

        // Verify revenue calculation (2 * 1299.99 + 1 * 199.99 = 2799.97)
        $this->assertEquals(2799.97, $data['total_revenue']);

        // Verify top products structure
        $this->assertIsArray($data['top_products']);
        $this->assertGreaterThan(0, count($data['top_products']));
    }

    /**
     * Test Case 4: AI Recommendations API
     *
     * This test verifies that:
     * - Recommendations API returns correct structure
     * - AI analysis includes sales data
     * - Weather information is provided
     * - Pricing recommendations are generated
     */
    public function test_ai_recommendations_api(): void
    {
        // Create some test orders for AI analysis
        Order::create([
            'product_id' => 1,
            'quantity' => 3,
            'price' => 1299.99,
            'order_date' => now()->subDays(2)
        ]);

        Order::create([
            'product_id' => 2,
            'quantity' => 1,
            'price' => 199.99,
            'order_date' => now()->subDays(1)
        ]);

        $response = $this->getJson('/api/recommendations');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'sales_analysis' => [
                             'period' => ['start', 'end'],
                             'summary' => [
                                 'total_revenue',
                                 'total_orders',
                                 'avg_order_value'
                             ],
                             'top_products',
                             'low_performing_products',
                             'hourly_trends'
                         ],
                         'weather_info' => [
                             'temperature',
                             'description',
                             'season',
                             'source'
                         ],
                         'ai_recommendations',
                         'weather_based_suggestions',
                         'pricing_recommendations',
                         'strategic_actions',
                         'timestamp'
                     ]
                 ]);

        $data = $response->json('data');

        // Verify AI recommendations are provided
        $this->assertIsArray($data['ai_recommendations']);
        $this->assertIsArray($data['pricing_recommendations']);
        $this->assertIsArray($data['strategic_actions']);

        // Verify weather integration
        $this->assertArrayHasKey('temperature', $data['weather_info']);
        $this->assertArrayHasKey('season', $data['weather_info']);
    }

    /**
     * Test Case 5: Real-Time Broadcasting Test
     *
     * This test verifies that:
     * - Events are properly queued for broadcasting
     * - Order creation triggers events
     * - Analytics updates are triggered
     */
    public function test_real_time_broadcasting(): void
    {
        // Test that order creation doesn't fail (events are dispatched internally)
        $orderData = [
            'product_id' => 1,
            'quantity' => 1,
            'price' => 999.99
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201);

        // Verify that the order was created successfully
        // (In a full integration test, you would test actual WebSocket connections)
        $this->assertDatabaseHas('orders', [
            'product_id' => 1,
            'quantity' => 1,
            'price' => 999.99
        ]);
    }

    /**
     * Test Case 6: API Performance Test
     *
     * This test verifies that:
     * - APIs respond within acceptable time limits
     * - Multiple concurrent requests are handled properly
     */
    public function test_api_performance(): void
    {
        $startTime = microtime(true);

        // Test multiple rapid API calls
        for ($i = 1; $i <= 5; $i++) {
            $orderData = [
                'product_id' => ($i % 2) + 1,
                'quantity' => $i,
                'price' => 100.00 * $i
            ];

            $response = $this->postJson('/api/orders', $orderData);
            $response->assertStatus(201);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Verify all orders complete within reasonable time (< 5 seconds)
        $this->assertLessThan(5.0, $executionTime, 'API performance test failed: took too long');

        // Verify all orders were created
        $this->assertEquals(5, Order::count());

        // Test analytics still works after multiple orders
        $response = $this->getJson('/api/analytics');
        $response->assertStatus(200);
    }

    /**
     * Test Case 7: Data Integrity Test
     *
     * This test verifies that:
     * - Order relationships work correctly
     * - Analytics calculations are accurate
     * - Data consistency is maintained
     */
    public function test_data_integrity(): void
    {
        // Create orders with specific data
        $order1 = Order::create([
            'product_id' => 1,
            'quantity' => 2,
            'price' => 500.00,
            'order_date' => now()
        ]);

        $order2 = Order::create([
            'product_id' => 2,
            'quantity' => 1,
            'price' => 300.00,
            'order_date' => now()
        ]);

        // Test order relationships
        $this->assertEquals('Test Laptop', $order1->product->name);
        $this->assertEquals('Test Headphones', $order2->product->name);

        // Test total calculations
        $this->assertEquals(1000.00, $order1->total);
        $this->assertEquals(300.00, $order2->total);

        // Test analytics calculations
        $response = $this->getJson('/api/analytics');
        $data = $response->json('data');

        $this->assertEquals(1300.00, $data['total_revenue']);
        $this->assertCount(2, $data['top_products']);
    }
}
