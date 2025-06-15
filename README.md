# 🚀 Real-Time Sales Dashboard

> A comprehensive real-time sales data management system with WebSocket broadcasting, built with Laravel and Laravel Reverb.

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![WebSocket](https://img.shields.io/badge/WebSocket-Reverb-green.svg)](https://reverb.laravel.com)
[![Database](https://img.shields.io/badge/Database-SQLite-blue.svg)](https://sqlite.org)
[![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)](#)

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

## 🧪 Testing

### Web Interface Testing
1. Open `http://127.0.0.1:8000/api-tester.html`
2. Click "Connect WebSocket" to establish real-time connection
3. Use the interface to test API endpoints
4. Watch real-time notifications for order events

### Manual API Testing
```powershell
# Create an order
$body = '{"product_id": 1, "quantity": 2, "price": 99.99}'
$headers = @{"Content-Type"="application/json"; "X-Requested-With"="XMLHttpRequest"}
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/orders" -Method POST -Body $body -Headers $headers

# Get analytics
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/analytics" -Method GET
```

### Debug Scripts
```powershell
# Test broadcasting functionality
php debug-broadcast.php

# Test WebSocket connectivity
php test-reverb-connection.php
```

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
