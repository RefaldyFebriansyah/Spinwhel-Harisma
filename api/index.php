<?php

use App\Models\WheelItem;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

// 1. Prepare writable /tmp directories for Vercel Serverless environment
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
    '/tmp/database',
];

foreach ($tmpDirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Prepare SQLite fallback database path if SQLite is used
$tmpDbPath = '/tmp/database/database.sqlite';
$bundledDbPath = __DIR__.'/../database/database.sqlite';

if (! file_exists($tmpDbPath)) {
    if (file_exists($bundledDbPath) && filesize($bundledDbPath) > 0) {
        @copy($bundledDbPath, $tmpDbPath);
    } else {
        @touch($tmpDbPath);
    }
}

// 3. Override Environment Variables for Vercel Serverless execution (Preserves cloud DB settings)
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');

putenv('LOG_CHANNEL=stderr');
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=file');
putenv('QUEUE_CONNECTION=sync');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');

if ($dbConn === 'sqlite') {
    putenv('DB_CONNECTION=sqlite');
    putenv('DB_DATABASE='.$tmpDbPath);
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $tmpDbPath;
}

$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['CACHE_STORE'] = 'file';
$_ENV['QUEUE_CONNECTION'] = 'sync';

// 4. Register Composer Autoloader & Bootstrap Laravel Application
require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// 5. Automatic Fallback: Auto-migrate & Auto-seed database if WheelItem count is 0
try {
    if (! Schema::hasTable('wheel_items') || WheelItem::count() === 0) {
        Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
    }
} catch (Throwable $e) {
    try {
        Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
    } catch (Throwable $ex) {
        // Ignore fallback errors
    }
}

// 6. Handle HTTP Request
$kernel = $app->make(Kernel::class);
$response = $kernel->handle(
    $request = Request::capture()
);
$response->send();
$kernel->terminate($request, $response);
