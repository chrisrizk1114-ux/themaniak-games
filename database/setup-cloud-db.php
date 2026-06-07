<?php

/**
 * Connect to free cloud MySQL/PostgreSQL and run migrations.
 * Usage: php database/setup-cloud-db.php
 *
 * Configure .env first — see database/cloud-db-setup.txt and .env.cloud.example
 */

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$driver = env('DB_CONNECTION', 'sqlite');

echo "The Maniak — cloud database setup\n";
echo "Driver: {$driver}\n\n";

if ($driver === 'sqlite') {
    echo "✗ .env is still on SQLite.\n";
    echo "  Copy settings from .env.cloud.example into .env, then run this again.\n";
    echo "  Guide: database/cloud-db-setup.txt\n";
    exit(1);
}

$host = env('DB_HOST');
$port = env('DB_PORT');
$database = env('DB_DATABASE');
$username = env('DB_USERNAME');

echo "Host: {$host}:{$port}\n";
echo "Database: {$database}\n";
echo "User: {$username}\n\n";

try {
    if ($driver === 'mysql') {
        $sslCa = env('MYSQL_ATTR_SSL_CA');
        if ($sslCa && ! is_file($root.'/'.str_replace('\\', '/', $sslCa)) && ! is_file($sslCa)) {
            echo "⚠ MYSQL_ATTR_SSL_CA file not found: {$sslCa}\n";
            echo "  Download CA certificate from Aiven and save as database/ssl/ca.pem\n\n";
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        if ($sslCa && (is_file($root.'/'.ltrim(str_replace('\\', '/', $sslCa), '/')) || is_file($sslCa))) {
            $caPath = is_file($sslCa) ? $sslCa : $root.'/'.ltrim(str_replace('\\', '/', $sslCa), '/');
            if (PHP_VERSION_ID >= 80500) {
                $options[Pdo\Mysql::ATTR_SSL_CA] = $caPath;
            } else {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
            }
            echo "Using SSL CA: {$caPath}\n";
        }

        $pdo = new PDO($dsn, env('DB_USERNAME'), env('DB_PASSWORD'), $options);
    } elseif ($driver === 'pgsql') {
        if (! extension_loaded('pdo_pgsql')) {
            echo "✗ pdo_pgsql is not enabled in PHP.\n";
            echo "  Enable extension=pdo_pgsql in php.ini and restart.\n";
            exit(1);
        }
        $ssl = env('DB_SSLMODE', 'require');
        $dsn = "pgsql:host={$host};port={$port};dbname={$database};sslmode={$ssl}";
        $pdo = new PDO($dsn, env('DB_USERNAME'), env('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    } else {
        echo "✗ Unsupported DB_CONNECTION: {$driver}\n";
        exit(1);
    }

    $pdo->query('SELECT 1');
    echo "✓ Connected to cloud database.\n\n";
} catch (Throwable $e) {
    echo "✗ Connection failed.\n";
    echo "  {$e->getMessage()}\n\n";
    echo "  See database/cloud-db-setup.txt\n";
    exit(1);
}

passthru(PHP_BINARY.' artisan config:clear', $c1);
passthru(PHP_BINARY.' artisan migrate --force', $c2);

if ($c2 === 0) {
    echo "\n✓ Migrations complete. Users, friends, sessions & cache tables are ready.\n";
    echo "  Register at your site — data is stored in the cloud.\n";
    exit(0);
}

exit($c2 ?: 1);
