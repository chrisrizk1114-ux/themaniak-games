#!/bin/sh
set -e
cd /var/www/html

export PORT="${PORT:-8000}"

mkdir -p storage/framework/sessions storage/framework/cache/data storage/framework/views storage/logs bootstrap/cache
mkdir -p /tmp/themaniak-sessions /tmp/themaniak-cache/data
chmod -R 777 storage bootstrap/cache /tmp/themaniak-sessions /tmp/themaniak-cache

php artisan optimize:clear
php artisan storage:link 2>/dev/null || true

php artisan migrate --force --no-interaction 2>/dev/null || true

exec php artisan serve --host=0.0.0.0 --port="${PORT}"
