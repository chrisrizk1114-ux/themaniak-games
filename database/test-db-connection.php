<?php

require dirname(__DIR__).'/vendor/autoload.php';

$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    Illuminate\Support\Facades\DB::connection()->getPdo();
    $userCount = Illuminate\Support\Facades\DB::table('users')->count();
    echo "DB OK — users: {$userCount}\n";
    exit(0);
} catch (Throwable $e) {
    echo 'DB FAIL: '.$e->getMessage()."\n";
    exit(1);
}
