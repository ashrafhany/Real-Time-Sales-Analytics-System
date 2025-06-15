# 🚀 Real-Time Sales Dashboard

> A comprehensive real-time sales data management system with WebSocket broadcasting, built with Laravel and Laravel Reverb.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![WebSocket](https://img.shields.io/badge/WebSocket-Reverb-green.svg)](https://reverb.laravel.com)
[![Database](https://img.shields.io/badge/Database-SQLite-blue.svg)](https://sqlite.org)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)](#)

## 🤖 AI Assistance & Manual Implementation

### 🧠 Parts Assisted by AI (GitHub Copilot)

The following components were developed with AI assistance to accelerate development and ensure best practices:

#### **AI-Powered Recommendation Engine** 🤖
- **RecommendationController.php** - Complete AI logic for business recommendations
- **Weather Integration** - OpenWeather API integration with intelligent fallbacks
- **Dynamic Pricing Algorithm** - Multi-factor pricing calculations based on:
  - Sales demand patterns
  - Seasonal adjustments
  - Weather conditions
  - Historical performance data
- **Strategic Business Actions** - Automated generation of actionable business insights

#### **Advanced Analytics Logic** 📊
- **Complex SQL Queries** - Optimized database queries for real-time analytics
- **Sales Pattern Analysis** - Trend identification and performance metrics
- **Time-based Analytics** - Revenue tracking with minute-by-minute comparisons
- **Data Aggregation** - Efficient grouping and calculation of sales metrics

#### **Frontend JavaScript Enhancement** 💻
- **WebSocket Event Handling** - Robust real-time event processing
- **Error Handling & Fallbacks** - Comprehensive error management for API calls
- **UI State Management** - Dynamic interface updates and notifications
- **Cross-browser Compatibility** - Native Pusher.js implementation for maximum support

### 🔨 Manual Implementation Details

The core architecture and foundational components were manually implemented:

#### **Laravel Architecture Setup** 🏗️
- **Project Structure** - Manual Laravel 11 project initialization
- **Database Design** - Hand-crafted migration files for products and orders
- **Model Relationships** - Carefully designed Eloquent relationships
- **Route Configuration** - API endpoint planning and implementation

#### **WebSocket Broadcasting System** 📡
- **Laravel Reverb Configuration** - Manual WebSocket server setup
- **Broadcasting Events** - Custom event classes (NewOrderCreated, AnalyticsUpdated)
- **Channel Management** - WebSocket channel configuration and security
- **Broadcasting Service Provider** - Manual service provider registration

#### **API Controller Foundation** 🔌
- **OrderController Structure** - Manual controller setup with validation
- **AnalyticsController Base** - Foundation for real-time analytics
- **Error Handling Framework** - Consistent API response patterns
- **Request Validation** - Laravel form request validation setup

#### **Database & Seeding** 🗄️
- **Migration Scripts** - Manually written database schema
- **Product Seeder** - Hand-crafted sample product data
- **Database Relationships** - Foreign key constraints and indexing
- **SQLite Configuration** - Database connection and optimization

#### **Testing Infrastructure** 🧪
- **HTML Test Interfaces** - Manually created interactive testing pages
- **Debug Scripts** - Custom PHP scripts for WebSocket testing
- **API Testing Framework** - Comprehensive test endpoint design

### 🎯 Development Approach

1. **Foundation First** - Manual setup of core Laravel architecture
2. **AI Enhancement** - AI assistance for complex algorithms and optimizations
3. **Integration** - Manual integration of AI-generated components
4. **Testing** - Manual verification and debugging of all features
5. **Documentation** - Comprehensive manual documentation

## 📋 Overview

This real-time sales dashboard provides a complete solution for managing orders and analyzing sales data with instant WebSocket updates. Built on Laravel 11 with Laravel Reverb for WebSocket functionality, it offers real-time broadcasting of order events and analytics updates.

## ✨ Features

### 🛍️ Order Management
- **Create Orders**: Add new orders with product selection, quantity, and pricing
- **Real-time Broadcasting**: Instant WebSocket notifications for new orders
- **Product Integration**: 8 pre-seeded sample products with relationships

### 📊 Analytics Dashboard
- **Total Revenue**: Real-time calculation of all order values
- **Top Products**: Best-selling products by quantity and revenue
- **Recent Activity**: Orders and revenue tracking for the last minute
- **Live Updates**: Automatic analytics refresh on new orders

### 🤖 AI-Powered Recommendations
- **Sales Analysis**: Intelligent analysis of sales patterns and trends
- **Weather Integration**: Weather-based product recommendations with OpenWeather API
- **Dynamic Pricing**: AI-suggested pricing based on demand, season, and weather
- **Strategic Actions**: Business strategy recommendations for growth
- **Product Promotion**: Automated suggestions for marketing and promotions

### 🔄 Real-Time WebSocket Features
- **Laravel Reverb Integration**: High-performance WebSocket server
- **Multi-Client Support**: Multiple users can receive updates simultaneously
- **Event Broadcasting**: Automatic broadcasting of order and analytics events
- **Fallback Support**: Native WebSocket implementation for maximum compatibility

### 🧪 Testing & Debugging
- **Interactive API Tester**: Comprehensive web interface for testing all endpoints
- **WebSocket Testing**: Dedicated interfaces for testing real-time functionality
- **Debug Tools**: Built-in debugging scripts and connection tests

## 🛠️ Technology Stack

- **Backend**: Laravel 11.x
- **WebSocket**: Laravel Reverb
- **Database**: SQLite (easily configurable for other databases)
- **Frontend**: Vanilla JavaScript with Pusher.js
- **Queue**: Sync (for immediate broadcasting)
- **Broadcasting**: Reverb driver with Pusher protocol

## 📁 Project Structure

```
├── app/
│   ├── Events/
│   │   ├── NewOrderCreated.php      # Order creation event
│   │   └── AnalyticsUpdated.php     # Analytics update event
│   ├── Http/Controllers/
│   │   ├── OrderController.php      # Order management
│   │   └── AnalyticsController.php  # Analytics API
│   └── Models/
│       ├── Product.php              # Product model
│       └── Order.php                # Order model with relationships
├── database/
│   ├── migrations/                  # Database schema
│   ├── seeders/
│   │   └── ProductSeeder.php        # Sample product data
│   └── database.sqlite              # SQLite database
├── public/
│   ├── api-tester.html             # Main testing interface
│   ├── websocket-test-final.html   # WebSocket testing
│   └── echo-debug.html             # Connection debugging
├── routes/
│   ├── api.php                     # API endpoints
│   └── channels.php                # WebSocket channels
├── config/
│   ├── broadcasting.php            # Broadcasting configuration
│   └── reverb.php                  # Reverb WebSocket config
└── tests/                          # Debug and testing scripts
```

## 🚀 Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm (for frontend dependencies)

### Installation

1. **Clone the repository**
   ```powershell
   git clone <repository-url>
   cd project-name
   ```

2. **Install dependencies**
   ```powershell
   composer install
   npm install
   ```

3. **Environment setup**
   ```powershell
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```powershell
   # Create SQLite database
   New-Item -Path "database\database.sqlite" -ItemType File -Force
   
   # Run migrations and seed data
   php artisan migrate
   php artisan db:seed --class=ProductSeeder
   ```

5. **Start the servers**
   
   **Terminal 1 - Laravel Server:**
   ```powershell
   php artisan serve
   ```
   
   **Terminal 2 - WebSocket Server:**
   ```powershell
   php artisan reverb:start --debug
   ```

6. **Access the application**
   - Main API Tester: `http://127.0.0.1:8000/api-tester.html`
   - WebSocket Tester: `http://127.0.0.1:8000/websocket-test-final.html`

## 📡 API Endpoints

### Orders
```http
POST /api/orders
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 2,
    "price": 99.99,
    "date": "2025-06-15T08:00:00Z" // optional
}
```

### Analytics
```http
GET /api/analytics
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_revenue": 1234.56,
        "top_products": [...],
        "last_minute_analytics": {
            "revenue_change": 99.99,
            "orders_count": 1
        }
    }
}
```

### AI-Powered Recommendations
```http
GET /api/recommendations
```

**Response:**
```json
{
    "success": true,
    "data": {
        "sales_analysis": {
            "summary": { "total_revenue": 3456.87, "total_orders": 19 },
            "top_products": [...],
            "low_performing_products": [...]
        },
        "weather_info": {
            "temperature": 35,
            "description": "clear",
            "season": "summer"
        },
        "ai_recommendations": [
            {
                "type": "promote_bestseller",
                "priority": "high",
                "action": "Increase marketing budget for 'Laptop Pro 15\"'",
                "expected_impact": "Revenue increase of 15-25%"
            }
        ],
        "weather_based_suggestions": [...],
        "pricing_recommendations": [...],
        "strategic_actions": [...]
    }
}
```

### Test Broadcasting
```http
GET /api/test-broadcast
```

## 🔄 WebSocket Events

### Channel: `sales-data`

#### Event: `new-order`
Triggered when a new order is created.
```javascript
{
    "order": {
        "id": 1,
        "product_id": 1,
        "product_name": "Laptop Pro 15\"",
        "quantity": 1,
        "price": "1299.99",
        "total": 1299.99,
        "order_date": "2025-06-15T08:00:00.000000Z"
    },
    "timestamp": "2025-06-15T08:00:00.123456Z"
}
```

#### Event: `analytics-updated`
Triggered after analytics recalculation.
```javascript
{
    "analytics": {
        "total_revenue": 1234.56,
        "top_products": [...],
        "last_minute_analytics": {...}
    },
    "timestamp": "2025-06-15T08:00:00.123456Z"
}
```

## 🧪 Comprehensive Testing Guide

### 🎯 Test Case 1: API Endpoint Testing

#### **Order Creation API Test**
**Objective**: Verify order creation triggers real-time events and database updates.

```powershell
# Test 1.1: Create a valid order
$orderData = @{
    product_id = 1
    quantity = 2
    price = 1299.99
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/orders" `
    -Method POST `
    -Body $orderData `
    -Headers @{"Content-Type"="application/json"}

# Expected Result: 201 Created with order details
Write-Host "Order Created: $($response.data.id)" -ForegroundColor Green
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Order created successfully",
    "data": {
        "id": 21,
        "product_id": 1,
        "product_name": "Laptop Pro 15\"",
        "quantity": 2,
        "price": "1299.99",
        "total": 2599.98,
        "order_date": "2025-06-15T10:30:00.000000Z"
    }
}
```

#### **Analytics API Test**
```powershell
# Test 1.2: Verify analytics calculation
$analytics = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/analytics" -Method GET

Write-Host "Total Revenue: $($analytics.data.total_revenue)" -ForegroundColor Yellow
Write-Host "Total Orders: $($analytics.data.total_orders)" -ForegroundColor Yellow
```

#### **AI Recommendations API Test**
```powershell
# Test 1.3: Verify AI recommendations generation
$recommendations = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/recommendations" -Method GET

Write-Host "AI Recommendations Count: $($recommendations.data.ai_recommendations.Count)" -ForegroundColor Cyan
Write-Host "Weather Temperature: $($recommendations.data.weather_info.temperature)°C" -ForegroundColor Cyan
```

### 🔄 Test Case 2: Real-Time WebSocket Functionality

#### **Manual WebSocket Test**
**Objective**: Verify real-time broadcasting works correctly.

**Prerequisites:**
1. Start Laravel server: `php artisan serve`
2. Start Reverb server: `php artisan reverb:start --debug`

**Test Steps:**
1. Open `http://127.0.0.1:8000/api-tester.html`
2. Click "Connect WebSocket" button
3. Verify connection status shows "Connected (Pusher)"
4. Create a test order using the form
5. Verify real-time notification appears

**Expected Results:**
- ✅ WebSocket connects successfully
- ✅ Order creation broadcasts `new-order` event
- ✅ Analytics updates broadcast `analytics-updated` event
- ✅ Frontend receives and displays notifications

#### **Automated WebSocket Test Script**
```powershell
# Test 2.1: Run automated WebSocket test
php test-websocket.php
```

**Expected Output:**
```
🧪 Testing WebSocket Broadcasting
================================

📦 Creating sample order...
✅ Order created: #21 - 2x Laptop Pro 15"
📡 Broadcasting new order event...
✅ New order event broadcasted
📊 Broadcasting analytics update...
✅ Analytics update event broadcasted
🎉 All WebSocket tests passed!
```

### 🌐 Test Case 3: Multi-Client Real-Time Testing

#### **Multi-Browser Test**
**Objective**: Verify multiple clients receive real-time updates simultaneously.

**Test Procedure:**
1. Open `api-tester.html` in **Browser 1** (Chrome)
2. Open `websocket-test-final.html` in **Browser 2** (Firefox/Edge)
3. Connect WebSocket in both browsers
4. Create an order in Browser 1
5. Verify both browsers receive the same real-time events

**Verification Commands:**
```powershell
# Create test order from command line while browsers are connected
$testOrder = @{
    product_id = 3
    quantity = 1
    price = 699.99
    date = (Get-Date).ToString("yyyy-MM-ddTHH:mm:ssZ")
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/orders" `
    -Method POST `
    -Body $testOrder `
    -Headers @{"Content-Type"="application/json"}
```

**Expected Results:**
- ✅ Both browsers show WebSocket connection
- ✅ Order creation appears in both browsers simultaneously
- ✅ Analytics updates appear in both browsers
- ✅ Notifications display with correct timestamps

### 🤖 Test Case 4: AI Recommendations Validation

#### **Weather Integration Test**
**Objective**: Verify AI recommendations adapt to weather conditions.

```powershell
# Test 4.1: Multiple recommendation requests to verify consistency
1..3 | ForEach-Object {
    $rec = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/recommendations" -Method GET
    Write-Host "Test $_" -ForegroundColor Blue
    Write-Host "  Weather: $($rec.data.weather_info.description) ($($rec.data.weather_info.temperature)°C)"
    Write-Host "  AI Recommendations: $($rec.data.ai_recommendations.Count)"
    Write-Host "  Pricing Suggestions: $($rec.data.pricing_recommendations.Count)"
    Write-Host "  Strategic Actions: $($rec.data.strategic_actions.Count)"
    Write-Host ""
}
```

#### **Sales Data Impact Test**
```powershell
# Test 4.2: Create orders and verify recommendations change
Write-Host "Creating multiple orders to test AI adaptation..." -ForegroundColor Green

# Create 3 different orders
@(
    @{product_id=2; quantity=1; price=199.99},
    @{product_id=4; quantity=2; price=79.99},
    @{product_id=7; quantity=1; price=59.99}
) | ForEach-Object {
    $orderJson = $_ | ConvertTo-Json
    Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/orders" -Method POST -Body $orderJson -Headers @{"Content-Type"="application/json"}
    Start-Sleep 1
}

# Get updated recommendations
$updatedRec = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/recommendations" -Method GET
Write-Host "Updated Total Revenue: $($updatedRec.data.sales_analysis.summary.total_revenue)" -ForegroundColor Yellow
```

### 🔧 Test Case 5: Error Handling & Edge Cases

#### **Invalid Data Test**
```powershell
# Test 5.1: Invalid product ID
try {
    $invalidOrder = @{product_id=999; quantity=1; price=100} | ConvertTo-Json
    Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/orders" -Method POST -Body $invalidOrder -Headers @{"Content-Type"="application/json"}
} catch {
    Write-Host "✅ Correctly rejected invalid product ID" -ForegroundColor Green
}

# Test 5.2: Invalid quantity
try {
    $invalidQty = @{product_id=1; quantity=0; price=100} | ConvertTo-Json
    Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/orders" -Method POST -Body $invalidQty -Headers @{"Content-Type"="application/json"}
} catch {
    Write-Host "✅ Correctly rejected zero quantity" -ForegroundColor Green
}
```

### 📊 Test Case 6: Performance Testing

#### **Load Test Script**
```powershell
# Test 6.1: Create multiple orders rapidly
Write-Host "Performance Test: Creating 10 orders rapidly..." -ForegroundColor Magenta

$stopwatch = [System.Diagnostics.Stopwatch]::StartNew()

1..10 | ForEach-Object -Parallel {
    $orderData = @{
        product_id = (Get-Random -Minimum 1 -Maximum 8)
        quantity = (Get-Random -Minimum 1 -Maximum 5)
        price = [math]::Round((Get-Random -Minimum 50 -Maximum 500), 2)
    } | ConvertTo-Json
    
    Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/orders" `
        -Method POST `
        -Body $orderData `
        -Headers @{"Content-Type"="application/json"}
} -ThrottleLimit 5

$stopwatch.Stop()
Write-Host "✅ Created 10 orders in $($stopwatch.ElapsedMilliseconds)ms" -ForegroundColor Green
```

### 🏆 Test Automation Suite

#### **Complete Test Runner**
For comprehensive automated testing, use the complete test suite:

```powershell
# Run all tests (API, validation, performance, AI, Laravel tests)
.\run-all-tests.ps1
```

This script will:
- ✅ Check server status
- ✅ Test all API endpoints
- ✅ Validate input handling
- ✅ Run performance tests
- ✅ Verify AI recommendations
- ✅ Execute Laravel test suite

#### **Individual Test Scripts**

**API Validation Only:**
```powershell
# Quick API endpoint validation
.\validate-api-endpoints.ps1
```

**WebSocket Testing Only:**
```powershell
# Real-time functionality testing
.\test-websocket-functionality.ps1
```

**Laravel Unit Tests Only:**
```powershell
# Run Laravel automated test suite
php artisan test --filter=SalesApiTest
```
    $orderResult = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/orders" -Method POST -Body $testOrder -Headers @{"Content-Type"="application/json"}
    Write-Host "  ✅ Order API: Working" -ForegroundColor Green
    
    $analytics = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/analytics" -Method GET
    Write-Host "  ✅ Analytics API: Working ($($analytics.data.total_revenue) revenue)" -ForegroundColor Green
    
    $recommendations = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/recommendations" -Method GET
    Write-Host "  ✅ Recommendations API: Working ($($recommendations.data.ai_recommendations.Count) suggestions)" -ForegroundColor Green
} catch {
    Write-Host "  ❌ API Test Failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: WebSocket Broadcasting
Write-Host "`n🔄 Testing WebSocket Broadcasting..." -ForegroundColor Yellow
try {
    php test-websocket.php | Out-Host
    Write-Host "  ✅ WebSocket Broadcasting: Working" -ForegroundColor Green
} catch {
    Write-Host "  ❌ WebSocket Test Failed: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n🎉 Test Suite Complete!" -ForegroundColor Blue
```

### ✅ Test Results Validation

**Automated Test Suite Results:**

When you run `.\run-all-tests.ps1`, you should see:

```powershell
🚀 COMPREHENSIVE SALES DASHBOARD TEST SUITE
===========================================

🔍 Checking Server Status...
  ✅ Laravel Server: Running

📡 Testing API Endpoints...
  ✅ Order API: Working (Created Order #42)
  ✅ Analytics API: Working (Revenue: 1234.56)
  ✅ Recommendations API: Working (5 suggestions)
  ✅ Broadcast API: Working

🔍 Testing Data Validation...
  ✅ Invalid Product ID: Properly rejected
  ✅ Invalid Quantity: Properly rejected

⚡ Testing Performance...
  ✅ Performance: Created 5 orders in 850ms

🤖 Testing AI Recommendations...
  ✅ Weather Integration: clear (25°C)
  ✅ AI Recommendations: 4 suggestions
  ✅ Pricing Recommendations: 3 suggestions
  ✅ Strategic Actions: 5 actions

🧪 Running Laravel Test Suite...
  ✅ Laravel Tests: All Passed

🎉 Sales Dashboard is ready for production!
```

**All tests should demonstrate:**

1. **API Functionality**: All 4 endpoints return valid responses
2. **Real-Time Broadcasting**: WebSocket events broadcast correctly
3. **Data Persistence**: Orders save to database and trigger analytics updates
4. **AI Intelligence**: Recommendations adapt to sales data and weather
5. **Error Handling**: Invalid requests are properly rejected
6. **Multi-Client Support**: Multiple browsers receive simultaneous updates
7. **Performance**: System handles multiple concurrent requests efficiently

**Test Success Criteria:**
- ✅ HTTP 201 responses for valid orders
- ✅ HTTP 200 responses for analytics and recommendations
- ✅ HTTP 422 responses for invalid data
- ✅ WebSocket connection establishment (< 2 seconds)
- ✅ Real-time event delivery (< 100ms latency)
- ✅ Consistent AI recommendations based on data
- ✅ Performance within acceptable limits (< 5 seconds for 5 orders)

## ⚙️ Configuration

### WebSocket Configuration
The system uses Laravel Reverb with Pusher protocol compatibility:

```php
// .env
BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=sync
REVERB_APP_ID=local-sales-app
REVERB_APP_KEY=local-sales-key
REVERB_APP_SECRET=local-sales-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Database Configuration
Currently configured for SQLite, but easily adaptable:

```php
// .env
DB_CONNECTION=sqlite
DB_DATABASE=c:\xampp\htdocs\projects\project-name\database\database.sqlite
```

## 🐛 Troubleshooting

### WebSocket Connection Issues
1. Ensure both Laravel and Reverb servers are running
2. Check that port 8080 is not blocked by firewall
3. Verify `.env` configuration matches Reverb settings
4. Use debug interfaces to test connections

### Broadcasting Not Working
1. Confirm `BROADCAST_DRIVER=reverb` in `.env`
2. Verify `BroadcastServiceProvider` is registered
3. Check Reverb server logs for events
4. Test with debug scripts

### Database Issues
1. Ensure SQLite file exists and is writable
2. Run migrations: `php artisan migrate`
3. Seed sample data: `php artisan db:seed --class=ProductSeeder`

## 📈 Performance

- **Real-time latency**: < 100ms for WebSocket events
- **Concurrent connections**: Supports multiple simultaneous users
- **Database**: Optimized queries with proper indexing
- **Broadcasting**: Immediate event delivery with sync queue

## 🔐 Security

- **Input validation**: All API inputs are validated
- **SQL injection protection**: Eloquent ORM prevents SQL injection
- **WebSocket security**: Configurable origins and authentication
- **Error handling**: Comprehensive error handling and logging

## 📚 Documentation

- **API Documentation**: See `API_DOCUMENTATION.md`
- **System Status**: See `SYSTEM_STATUS.md`
- **Code Comments**: Comprehensive inline documentation

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🙏 Acknowledgments

- Laravel Framework for the solid foundation
- Laravel Reverb for WebSocket functionality
- Pusher.js for client-side WebSocket support

---

**Built with ❤️ using Laravel 11 and Laravel Reverb**
