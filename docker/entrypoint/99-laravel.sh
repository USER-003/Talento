#!/bin/sh
set -e

cd /app || exit 0

# Ensure necessary directories exist and have correct permissions
mkdir -p \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs \
  bootstrap/cache

chown -R application:application storage bootstrap/cache || true
chmod -R ug+rwX storage bootstrap/cache || true

# Clear and rebuild caches with real environment variables available at runtime
composer install --no-dev --optimize-autoloader || true
composer require pusher/pusher-php-server || true
php artisan config:clear || true
php artisan route:clear  || true
php artisan view:clear   || true
php artisan cache:clear  || true
php artisan optimize     || true
