# Sales Data Management System API

This system provides real-time sales data management and analytics capabilities with WebSocket support for live updates.

## Real-Time WebSocket Features

### WebSocket Connection
- **Server**: Laravel Reverb WebSocket server running on `ws://localhost:8080`
- **Channel**: `sales-data` - Public channel for real-time sales updates
- **Events**: 
  - `new-order` - Triggered when a new order is created
  - `analytics-updated` - Triggered when sales analytics are updated

### WebSocket Events

#### New Order Event (`new-order`)
Broadcasted automatically when a new order is created via the POST /api/orders endpoint.

**Event Data:**
```json
{
    "order": {
        "id": 1,
        "product_id": 1,
        "product_name": "Laptop Pro 15\"",
        "quantity": 2,
        "price": "99.99",
        "total": 199.98,
        "order_date": "2025-06-15T10:30:00.000000Z",
        "created_at": "2025-06-15T10:30:00.000000Z"
    },
    "timestamp": "2025-06-15T10:30:00.000000Z"
}
```

#### Analytics Updated Event (`analytics-updated`)
Broadcasted automatically when analytics data changes due to new orders.

**Event Data:**
```json
{
    "analytics": {
        "total_revenue": 15432.50,
        "top_products": [...],
        "last_minute_analytics": {
            "revenue_change": 199.98,
            "orders_count": 1,
            "revenue_change_from_previous_minute": 199.98,
            "revenue_change_percentage": 100
        },
        "timestamp": "2025-06-15T10:30:00.000000Z"
    },
    "timestamp": "2025-06-15T10:30:00.000000Z"
}
```

### Frontend Integration

#### Using Laravel Echo (Recommended)
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const echo = new Echo({
    broadcaster: 'reverb',
    key: 'local-sales-key',
    wsHost: 'localhost',
    wsPort: 8080,
    forceTLS: false,
});

// Subscribe to sales data channel
echo.channel('sales-data')
    .listen('new-order', (data) => {
        console.log('New order:', data.order);
        // Update UI with new order
    })
    .listen('analytics-updated', (data) => {
        console.log('Analytics updated:', data.analytics);
        // Update analytics dashboard
    });
```

#### Using Native WebSocket
```javascript
const ws = new WebSocket('ws://localhost:8080/app/local-sales-key?protocol=7&client=js&version=8.0.0');

ws.onopen = function() {
    // Subscribe to channel
    ws.send(JSON.stringify({
        event: 'pusher:subscribe',
        data: { channel: 'sales-data' }
    }));
};

ws.onmessage = function(event) {
    const data = JSON.parse(event.data);
    if (data.event === 'new-order') {
        console.log('New order:', data.data);
    }
};
```

## Endpoints

### 1. Create Order
**POST** `/api/orders`

Creates a new order in the system.

**Request Body:**
```json
{
    "product_id": 1,
    "quantity": 2,
    "price": 99.99,
    "date": "2025-06-15T10:30:00Z" // Optional, defaults to current time
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Order created successfully",
    "data": {
        "id": 1,
        "product_id": 1,
        "product_name": "Laptop Pro 15\"",
        "quantity": 2,
        "price": "99.99",
        "total": 199.98,
        "order_date": "2025-06-15T10:30:00.000000Z",
        "created_at": "2025-06-15T10:30:00.000000Z"
    }
}
```

**Validation Rules:**
- `product_id`: Required, must exist in products table
- `quantity`: Required, minimum value 1
- `price`: Required, minimum value 0
- `date`: Optional, must be valid date format

### 2. Get Analytics
**GET** `/api/analytics`

Returns real-time sales insights and analytics.

**Response (200):**
```json
{
    "success": true,
    "data": {
        "total_revenue": 15432.50,
        "top_products": [
            {
                "product_id": 1,
                "product_name": "Laptop Pro 15\"",
                "total_revenue": 5199.96,
                "total_quantity_sold": 4
            },
            {
                "product_id": 3,
                "product_name": "Smartphone X",
                "total_revenue": 2799.96,
                "total_quantity_sold": 4
            }
        ],
        "last_minute_analytics": {
            "revenue_change": 199.98,
            "orders_count": 1,
            "revenue_change_from_previous_minute": 199.98,
            "revenue_change_percentage": 100,
            "time_range": {
                "from": "2025-06-15T10:29:00.000000Z",
                "to": "2025-06-15T10:30:00.000000Z"
            }
        },
        "timestamp": "2025-06-15T10:30:00.000000Z"
    }
}
```

**Analytics Includes:**
- **Total Revenue**: Sum of all orders (quantity × price)
- **Top Products**: Products ranked by total revenue generated
- **Last Minute Revenue**: Revenue generated in the last 60 seconds
- **Last Minute Orders**: Number of orders placed in the last 60 seconds
- **Revenue Change**: Comparison with the previous minute

### 3. Get AI-Powered Recommendations
**GET** `/api/recommendations`

Returns intelligent product recommendations based on sales data analysis and weather conditions.

**Response (200):**
```json
{
    "success": true,
    "data": {
        "sales_analysis": {
            "period": {
                "start": "2025-06-08T09:09:35.217573Z",
                "end": "2025-06-15T09:09:35.217573Z"
            },
            "summary": {
                "total_revenue": 3456.87,
                "total_orders": 19,
                "avg_order_value": 181.94
            },
            "top_products": [...],
            "low_performing_products": [...],
            "hourly_trends": [...]
        },
        "weather_info": {
            "temperature": 35,
            "feels_like": 30,
            "humidity": 53,
            "description": "clear",
            "season": "summer",
            "source": "mock"
        },
        "ai_recommendations": [
            {
                "type": "promote_bestseller",
                "priority": "high",
                "action": "Increase marketing budget for 'Laptop Pro 15\"' as it's generating the highest revenue",
                "expected_impact": "Revenue increase of 15-25%",
                "reasoning": "Top-performing product with proven market demand"
            }
        ],
        "weather_based_suggestions": [
            {
                "trigger": "hot_weather",
                "temperature": 35,
                "products": ["Bluetooth Speaker", "Tablet Air"],
                "action": "Promote portable electronics for outdoor activities",
                "discount_suggestion": "15% off summer electronics bundle"
            }
        ],
        "pricing_recommendations": [...],
        "strategic_actions": [...]
    }
}
```

**Recommendations Include:**
- **Sales Analysis**: Comprehensive analysis of recent sales patterns
- **Weather Integration**: Weather-based product suggestions with OpenWeather API
- **AI Recommendations**: Intelligent marketing and promotion suggestions
- **Dynamic Pricing**: AI-suggested pricing based on demand and weather
- **Strategic Actions**: Business strategy recommendations for growth

## Error Responses

**Validation Error (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "product_id": ["The product id field is required."],
        "quantity": ["The quantity must be at least 1."]
    }
}
```

**Server Error (500):**
```json
{
    "success": false,
    "message": "Failed to create order",
    "error": "Error details..."
}
```

## Sample Usage

### Create an Order
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2,
    "price": 1299.99
  }'
```

### Get Analytics
```bash
curl -X GET http://localhost:8000/api/analytics
```

### Get AI Recommendations
```bash
curl -X GET http://localhost:8000/api/recommendations
```

## Available Products (Seeded)

The system comes pre-seeded with the following products:

1. Laptop Pro 15" - $1,299.99
2. Wireless Headphones - $199.99
3. Smartphone X - $699.99
4. Gaming Mouse - $79.99
5. Mechanical Keyboard - $129.99
6. 4K Monitor - $399.99
7. Bluetooth Speaker - $59.99
8. Tablet Air - $549.99

Use these product IDs when creating orders.
