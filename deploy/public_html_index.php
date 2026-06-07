<?php

/**
 * GoDaddy cPanel — use when document root must stay public_html
 * but Laravel lives in ~/themaniak-app/
 *
 * 1. Upload this file to public_html/index.php
 * 2. Replace YOUR_CPANEL_USER with your cPanel username (e.g. j3x9k2)
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$laravelRoot = '/home/YOUR_CPANEL_USER/themaniak-app';

if (file_exists($maintenance = $laravelRoot.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $laravelRoot.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $laravelRoot.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
