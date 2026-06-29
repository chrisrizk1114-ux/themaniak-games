#!/bin/sh
set -eu

cd /var/www/html

export PORT="${PORT:-8000}"

mkdir -p storage/framework/sessions storage/framework/cache/data storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

php artisan storage:link 2>/dev/null || true

if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY is not set. Configure it in the Render dashboard."
    exit 1
fi

echo "Waiting for database..."
db_ready=0
i=1
while [ "$i" -le 30 ]; do
    if php artisan migrate --force --no-interaction; then
        echo "Database ready."
        db_ready=1
        break
    fi
    echo "Database not ready yet (attempt $i/30)..."
    i=$((i + 1))
    sleep 2
done

if [ "$db_ready" -eq 0 ]; then
    echo "WARNING: Database migrations did not complete. Starting server anyway."
fi

php artisan config:cache 2>/dev/null || true

echo "Starting server on 0.0.0.0:${PORT}..."
exec php -d memory_limit=256M artisan serve --host=0.0.0.0 --port="${PORT}"
