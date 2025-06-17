<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Events\NewOrderCreated;
use App\Events\AnalyticsUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Display a listing of the user's orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('product')->latest()->get();

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'quantity' => $validated['quantity'],
            'total_price' => $product->price * $validated['quantity'],
        ]);

        // Broadcast events for real-time updates
        NewOrderCreated::dispatch($order);

        // Update analytics and broadcast
        $analytics = $this->getAnalyticsSummary();
        AnalyticsUpdated::dispatch($analytics);

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load('product')
        ], 201);
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $order = Order::with('product')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Get analytics summary for events
     *
     * @return array
     */
    private function getAnalyticsSummary()
    {
        $totalSales = Order::sum('total_price');
        $orderCount = Order::count();
        $topProducts = DB::table('orders')
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(orders.quantity) as total_quantity'))
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'total_sales' => $totalSales,
            'order_count' => $orderCount,
            'top_products' => $topProducts,
        ];
    }
}
