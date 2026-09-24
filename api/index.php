<?php

use Illuminate\Http\Request;

// 1. Prepare writable /tmp directories for Vercel Serverless environment
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
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

// 2. Prepare writable SQLite Database in /tmp/database/database.sqlite
$tmpDbPath = '/tmp/database/database.sqlite';
$bundledDbPath = __DIR__.'/../database/database.sqlite';

if (file_exists($bundledDbPath) && filesize($bundledDbPath) > 0) {
    @copy($bundledDbPath, $tmpDbPath);
} elseif (! file_exists($tmpDbPath)) {
    @touch($tmpDbPath);
}

// 3. Override Environment Variables for Vercel Serverless execution
putenv('APP_KEY=base64:pj8GOVGfD4zbNzq5aYv3VeDSMyhJpvG86hR1q5kit/g=');
putenv('APP_DEBUG=true');
putenv('LOG_CHANNEL=stderr');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE='.$tmpDbPath);
putenv('SESSION_DRIVER=cookie');
putenv('CACHE_STORE=array');
putenv('QUEUE_CONNECTION=sync');
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');

$_ENV['APP_KEY'] = 'base64:pj8GOVGfD4zbNzq5aYv3VeDSMyhJpvG86hR1q5kit/g=';
$_ENV['APP_DEBUG'] = 'true';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $tmpDbPath;
$_ENV['SESSION_DRIVER'] = 'cookie';
$_ENV['CACHE_STORE'] = 'array';
$_ENV['QUEUE_CONNECTION'] = 'sync';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Fix Vercel SCRIPT_NAME / SCRIPT_FILENAME so Laravel resolves routes dynamically
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = __DIR__.'/../public/index.php';

// 4. Register Composer Autoloader & Bootstrap Laravel Application
require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// 5. Handle HTTP Request
$app->handleRequest(Request::capture());
