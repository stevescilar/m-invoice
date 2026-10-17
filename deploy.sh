#!/bin/sh
export PATH=/usr/local/bin:/usr/bin:/bin:$PATH

if [ ! -f .env ] && [ -f .env.production.example ]; then
    cp .env.production.example .env
fi

if [ -f composer.phar ]; then
    php composer.phar install --no-dev --optimize-autoloader --no-interaction
elif command -v composer >/dev/null 2>&1; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

php artisan storage:link || true
php artisan migrate --force || true
php artisan optimize:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
