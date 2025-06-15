<?php

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Reverb Connection ===\n";

// Test direct connection to Reverb
$config = config('broadcasting.connections.reverb');
$host = $config['options']['host'];
$port = $config['options']['port'];
$scheme = $config['options']['scheme'];

echo "Testing connection to {$scheme}://{$host}:{$port}\n";

// Test if we can reach the Reverb server
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "{$scheme}://{$host}:{$port}/app/{$config['key']}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_NOBODY, true);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "✗ cURL error: $error\n";
} else {
    echo "✓ HTTP response code: $httpCode\n";
}

// Test Laravel's broadcasting connection
echo "\nTesting Laravel broadcasting configuration...\n";

try {
    $broadcaster = app('Illuminate\Broadcasting\BroadcastManager')->driver('reverb');
    echo "✓ Reverb broadcaster created successfully\n";
    echo "Broadcaster class: " . get_class($broadcaster) . "\n";
} catch (Exception $e) {
    echo "✗ Failed to create broadcaster: " . $e->getMessage() . "\n";
}

echo "=== Connection Test Complete ===\n";
