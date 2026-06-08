#!/bin/sh
cd /var/www/html

export PORT="${PORT:-8000}"

mkdir -p storage/framework/sessions storage/framework/cache/data storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan storage:link 2>/dev/null || true
php artisan migrate --force --no-interaction 2>/dev/null || true

exec php artisan serve --host=0.0.0.0 --port="${PORT}"
