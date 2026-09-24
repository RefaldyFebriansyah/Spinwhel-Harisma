<?php

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

// 2. Prepare writable SQLite Database in /tmp/database/database.sqlite
$tmpDbPath = '/tmp/database/database.sqlite';
$bundledDbPath = __DIR__.'/../database/database.sqlite';

if (! file_exists($tmpDbPath)) {
    if (file_exists($bundledDbPath)) {
        @copy($bundledDbPath, $tmpDbPath);
    } else {
        @touch($tmpDbPath);
    }
}

// 3. Override Environment Variables for Vercel Serverless execution
putenv('LOG_CHANNEL=stderr');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE='.$tmpDbPath);
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');

$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $tmpDbPath;

// 4. Forward request to Laravel public/index.php
require __DIR__.'/../public/index.php';
