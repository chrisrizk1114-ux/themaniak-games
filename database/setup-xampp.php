<?php

/**
 * Creates the MySQL database from .env and runs migrations.
 * Usage: php database/setup-xampp.php
 */

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$host = env('DB_HOST', '127.0.0.1');
$port = env('DB_PORT', '3306');
$database = env('DB_DATABASE', 'game_adventure');
$username = env('DB_USERNAME', 'root');
$password = env('DB_PASSWORD', '');

echo "Game Adventure — XAMPP database setup\n";
echo "Host: {$host}:{$port}\n";
echo "Database: {$database}\n\n";

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $pdo->exec(
        "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
    );
    echo "✓ Database '{$database}' is ready.\n\n";
} catch (PDOException $e) {
    echo "✗ Could not connect to MySQL.\n";
    echo "  Make sure MySQL is running in XAMPP.\n";
    echo "  Error: {$e->getMessage()}\n";
    exit(1);
}

passthru(PHP_BINARY.' artisan config:clear', $code1);
passthru(PHP_BINARY.' artisan migrate --force', $code2);

if ($code2 === 0) {
    echo "\n✓ All tables created. Open phpMyAdmin → database: {$database}\n";
    exit(0);
}

exit($code2 ?: 1);
