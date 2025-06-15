# WebSocket Real-Time Functionality Test Script
# Tests the real-time broadcasting capabilities of the sales dashboard

Write-Host "🔄 WEBSOCKET REAL-TIME TESTING" -ForegroundColor Blue
Write-Host "==============================" -ForegroundColor Blue

# Check if Reverb server is running
Write-Host "`n🔍 Checking WebSocket Server..." -ForegroundColor Yellow

try {
    $wsCheck = Test-NetConnection -ComputerName "localhost" -Port 8080 -WarningAction SilentlyContinue
    if ($wsCheck.TcpTestSucceeded) {
        Write-Host "  ✅ Reverb WebSocket Server: Running on port 8080" -ForegroundColor Green
    } else {
        Write-Host "  ❌ Reverb WebSocket Server: Not running" -ForegroundColor Red
        Write-Host "     Please start with: php artisan reverb:start --debug" -ForegroundColor Yellow
        Write-Host "     Then run this test again." -ForegroundColor Yellow
        exit 1
    }
} catch {
    Write-Host "  ❌ Could not check WebSocket server status" -ForegroundColor Red
    exit 1
}

# Test broadcast functionality
Write-Host "`n📡 Testing Broadcast Functionality..." -ForegroundColor Yellow

try {
    # Run the PHP WebSocket test script
    $phpTestResult = & php test-websocket.php
    Write-Host $phpTestResult
    Write-Host "  ✅ PHP WebSocket Test: Completed" -ForegroundColor Green
} catch {
    Write-Host "  ❌ PHP WebSocket Test Failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Create test orders and verify broadcasting
Write-Host "`n🛍️ Creating Test Orders for Real-Time Broadcasting..." -ForegroundColor Yellow

$testOrders = @(
    @{product_id=1; quantity=1; price=299.99},
    @{product_id=2; quantity=2; price=149.99},
    @{product_id=3; quantity=1; price=79.99}
)

foreach ($order in $testOrders) {
    try {
        $orderJson = $order | ConvertTo-Json
        $result = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/orders" `
            -Method POST `
            -Body $orderJson `
            -Headers @{"Content-Type"="application/json"}

        Write-Host "  ✅ Order Created: #$($result.data.id) - $($result.data.quantity)x $($result.data.product_name)" -ForegroundColor Green

        # Small delay to allow broadcasting
        Start-Sleep -Milliseconds 500

    } catch {
        Write-Host "  ❌ Order Creation Failed: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Test manual broadcast
Write-Host "`n📢 Testing Manual Broadcast..." -ForegroundColor Yellow
try {
    $broadcast = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/test-broadcast" -Method GET
    Write-Host "  ✅ Manual Broadcast: Sent successfully" -ForegroundColor Green
} catch {
    Write-Host "  ❌ Manual Broadcast Failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Instructions for manual verification
Write-Host "`n👀 MANUAL VERIFICATION STEPS" -ForegroundColor Blue
Write-Host "=============================" -ForegroundColor Blue
Write-Host "1. Open your browser to: http://127.0.0.1:8000/api-tester.html" -ForegroundColor Yellow
Write-Host "2. Click 'Connect WebSocket' button" -ForegroundColor Yellow
Write-Host "3. Create an order using the form" -ForegroundColor Yellow
Write-Host "4. Verify you see real-time notifications" -ForegroundColor Yellow
Write-Host "5. Open a second browser tab with the same page" -ForegroundColor Yellow
Write-Host "6. Verify both tabs receive the same notifications" -ForegroundColor Yellow

Write-Host "`n📋 Expected WebSocket Events:" -ForegroundColor White
Write-Host "- 'new-order' event when orders are created" -ForegroundColor Gray
Write-Host "- 'analytics-updated' event after each order" -ForegroundColor Gray
Write-Host "- Connection status indicators" -ForegroundColor Gray
Write-Host "- Real-time notifications in the UI" -ForegroundColor Gray

Write-Host "`n🎯 WebSocket Testing Complete!" -ForegroundColor Blue
