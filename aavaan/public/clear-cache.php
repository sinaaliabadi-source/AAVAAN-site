<?php
/**
 * Cache cleaner for shared hosting (no SSH).
 * SECURITY: DELETE THIS FILE after use!
 */
$secret = 'aavaan2024clear';
if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403);
    die('Forbidden. Add ?key=aavaan2024clear');
}

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$results = [];

// Clear bootstrap/cache files
$cacheDir = __DIR__ . '/../bootstrap/cache/';
foreach (glob($cacheDir . '*.php') as $file) {
    @unlink($file);
    $results[] = '✓ Deleted: bootstrap/cache/' . basename($file);
}

// Clear storage/framework cache
$storageCacheDirs = [
    __DIR__ . '/../storage/framework/cache/data/',
    __DIR__ . '/../storage/framework/views/',
];
foreach ($storageCacheDirs as $dir) {
    if (is_dir($dir)) {
        foreach (glob($dir . '*') as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        $results[] = '✓ Cleared: ' . str_replace(__DIR__ . '/../', '', $dir);
    }
}

// Try artisan commands
try {
    Artisan::call('config:clear');
    $results[] = '✓ config:clear';
} catch (Exception $e) {
    $results[] = '✗ config:clear: ' . $e->getMessage();
}

try {
    Artisan::call('route:clear');
    $results[] = '✓ route:clear';
} catch (Exception $e) {
    $results[] = '✗ route:clear: ' . $e->getMessage();
}

try {
    Artisan::call('view:clear');
    $results[] = '✓ view:clear';
} catch (Exception $e) {
    $results[] = '✗ view:clear: ' . $e->getMessage();
}

try {
    Artisan::call('cache:clear');
    $results[] = '✓ cache:clear';
} catch (Exception $e) {
    $results[] = '✗ cache:clear: ' . $e->getMessage();
}

// Check if new controllers exist
$controllers = [
    'AdminUserController',
    'AdminArtistController',
    'AdminVerificationController',
    'AdminProductionController',
    'AdminSubscriptionController',
    'AdminPaymentController',
    'AdminDiscountController',
    'AdminSystemSettingController',
];
$results[] = '';
$results[] = '=== Controller File Check ===';
foreach ($controllers as $ctrl) {
    $path = __DIR__ . '/../app/Http/Controllers/Admin/' . $ctrl . '.php';
    $results[] = (file_exists($path) ? '✓ EXISTS' : '✗ MISSING') . ': ' . $ctrl;
}

echo '<pre style="font-family:monospace;font-size:14px;padding:20px;">';
echo "=== Cache Cleared ===\n";
echo implode("\n", $results);
echo "\n\n⚠️  DELETE public/clear-cache.php now!";
echo '</pre>';
