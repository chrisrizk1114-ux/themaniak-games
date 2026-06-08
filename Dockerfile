FROM node:20-bookworm-slim AS assets

WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
RUN npm ci

COPY resources ./resources
RUN npm run build

FROM php:8.2-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx gettext-base git unzip libzip-dev libonig-dev \
    && docker-php-ext-install pdo_mysql zip opcache mbstring \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default

COPY deploy/php-opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY deploy/nginx.conf.template /etc/nginx/nginx.conf.template

RUN sed -i 's/^pm.max_children = .*/pm.max_children = 6/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^pm.start_servers = .*/pm.start_servers = 2/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^pm.min_spare_servers = .*/pm.min_spare_servers = 1/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^pm.max_spare_servers = .*/pm.max_spare_servers = 3/' /usr/local/etc/php-fpm.d/www.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .

COPY --from=assets /app/public/build ./public/build

ENV APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=
RUN composer dump-autoload --optimize --classmap-authoritative \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan event:cache \
    && mkdir -p storage/framework/sessions storage/framework/cache/data storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x deploy/docker-start.sh
ENV APP_KEY=

ENV PORT=8000
EXPOSE 8000

CMD ["deploy/docker-start.sh"]
