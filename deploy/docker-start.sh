#!/bin/sh
set -e
cd /var/www/html

export PORT="${PORT:-8000}"

# Runtime config only (needs live env vars from Render)
php artisan config:cache
php artisan storage:link 2>/dev/null || true

# Migrations run via Render preDeployCommand — skip here for faster cold starts.
if [ "${RUN_MIGRATE_ON_START:-0}" = "1" ]; then
    php artisan migrate --force --no-interaction
fi

envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /tmp/nginx.conf

php-fpm -D
exec nginx -c /tmp/nginx.conf -g 'daemon off;'
