#!/bin/sh
set -e
cd /var/www/html

export PORT="${PORT:-8000}"

mkdir -p storage/framework/sessions storage/framework/cache/data storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

php artisan optimize:clear
php artisan config:cache
php artisan storage:link 2>/dev/null || true

if [ "${RUN_MIGRATE_ON_START:-0}" = "1" ]; then
    php artisan migrate --force --no-interaction
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT}"
