#!/bin/sh
set -e
cd /var/www/html

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link 2>/dev/null || true
php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
