<?php

// Include the Composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Display current Laravel Reverb configuration
echo "---- REVERB CONFIGURATION ----\n";
echo "App ID: " . config('reverb.apps.apps.0.id') . "\n";
echo "App Key: " . config('reverb.apps.apps.0.key') . "\n";
echo "Secret: " . substr(config('reverb.apps.apps.0.secret'), 0, 5) . "...\n";
echo "Host: " . config('reverb.apps.apps.0.options.host') . "\n";
echo "Port: " . config('reverb.apps.apps.0.options.port') . "\n";
echo "Scheme: " . config('reverb.apps.apps.0.options.scheme') . "\n";
echo "---------------------------\n\n";

// Test broadcast
try {
    echo "Broadcasting test event to channel 'sales-data'...\n";

    // Create test data
    $testData = [
        'message' => 'Test broadcast from PHP script',
        'timestamp' => date('Y-m-d H:i:s'),
        'test_id' => uniqid()
    ];

    // Broadcast using AnalyticsUpdated event
    event(new App\Events\AnalyticsUpdated($testData));

    echo "Broadcast completed successfully!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

// Display channel information if accessible
try {
    echo "\n---- CHANNELS INFO ----\n";
    $channels = Illuminate\Support\Facades\Broadcast::driver()->getChannels();
    print_r($channels);
} catch (Exception $e) {
    echo "Could not get channels info: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
