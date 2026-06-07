#!/usr/bin/env bash
# Deploy The Maniak games to themaniak.online (run ON THE SERVER after upload)
set -euo pipefail

APP_DIR="${1:-/var/www/themaniak.online}"

echo "==> Deploying to ${APP_DIR}"
cd "${APP_DIR}"

if [[ ! -f .env ]]; then
    echo "==> Creating .env from deploy/env.production"
    cp deploy/env.production .env
    echo "!! Edit .env: DB password, APP_KEY (php artisan key:generate), Google OAuth if used"
fi

echo "==> Composer (production)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Laravel setup"
if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --force
fi
php artisan migrate --force
php artisan storage:link 2>/dev/null || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Permissions"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

echo "==> Done. Site: https://themaniak.online"
echo "    Health check: https://themaniak.online/up"
