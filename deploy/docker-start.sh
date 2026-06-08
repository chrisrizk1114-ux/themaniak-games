#!/bin/sh
set -e
cd /var/www/html

# Runtime config only (needs live env vars from Render)
php artisan config:cache

# Migrations run via Render preDeployCommand — skip here for faster cold starts.
# Fallback if preDeployCommand is not configured:
if [ "${RUN_MIGRATE_ON_START:-0}" = "1" ]; then
    php artisan migrate --force --no-interaction
fi

php artisan storage:link 2>/dev/null || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
