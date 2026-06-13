#!/bin/sh
cd /var/www/html

export PORT="${PORT:-8000}"

mkdir -p storage/framework/sessions storage/framework/cache/data storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php bootstrap/cache/events.php 2>/dev/null || true
php artisan optimize:clear 2>/dev/null || true
php artisan storage:link 2>/dev/null || true

echo "Waiting for database..."
i=1
while [ "$i" -le 15 ]; do
    if php artisan migrate --force --no-interaction 2>/dev/null; then
        echo "Database ready."
        break
    fi
    echo "Database not ready yet (attempt $i/15)..."
    i=$((i + 1))
    sleep 2
done

exec php artisan serve --host=0.0.0.0 --port="${PORT}"
