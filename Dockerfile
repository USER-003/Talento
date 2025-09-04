#######################################################################
# Simple, production-ready Dockerfile for Laravel 11 on Koyeb
#######################################################################

# 1) Build frontend assets with Vite
FROM node:20-alpine AS build-assets
WORKDIR /app

COPY package*.json ./
RUN npm ci || npm install

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build


# 2) PHP + Nginx runtime image (PHP 8.2 for Laravel 11)
FROM webdevops/php-nginx:8.2-alpine

# System libs and PHP extensions commonly needed by Laravel
RUN apk add --no-cache libzip-dev oniguruma-dev postgresql-dev \
 && docker-php-ext-install bcmath mbstring pdo_mysql pdo_pgsql zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Nginx document root and environment
ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production

WORKDIR /app

# Copy application code
COPY . .

# Bring built assets
COPY --from=build-assets /app/public/build ./public/build

# Install PHP deps and prepare writable dirs (no build-time caches)
RUN set -e; \
    export COMPOSER_ALLOW_SUPERUSER=1; \
    if ! composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader --no-ansi --no-progress; then \
        echo "composer install failed (likely lock mismatch). Running composer update for prod deps..."; \
        composer update --no-interaction --no-dev --prefer-dist --optimize-autoloader --no-ansi --no-progress; \
    fi; \
    mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache; \
    chown -R application:application storage bootstrap/cache; \
    chmod -R ug+rwX storage bootstrap/cache; \
    php artisan storage:link || true

# Health check and runtime bootstrap script (sh-compatible)
RUN set -e; \
    printf "ok" > /app/public/health; \
    cat > /opt/docker/provision/entrypoint.d/99-laravel.sh <<'EOF' \
    && sed -i 's/\r$//' /opt/docker/provision/entrypoint.d/99-laravel.sh \
    && chmod +x /opt/docker/provision/entrypoint.d/99-laravel.sh
#!/bin/sh
cd /app || exit 0

# Ensure directories and permissions
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R application:application storage bootstrap/cache || true
chmod -R ug+rwX storage bootstrap/cache || true

# Clear and rebuild caches safely with real envs
php artisan config:clear || true
php artisan cache:clear  || true
php artisan route:clear  || true
php artisan view:clear   || true

# Run migrations only if explicitly enabled and DB env is present
if [ "${MIGRATE_ON_BOOT:-0}" = "1" ]; then
    if [ -n "${DB_CONNECTION:-}" ] && [ -n "${DB_HOST:-}" ] && [ -n "${DB_DATABASE:-}" ] && [ -n "${DB_USERNAME:-}" ]; then
        php artisan migrate --force || echo "[entrypoint] Migrations failed (likely insufficient privileges); continuing startup"
    else
        echo "[entrypoint] DB env vars missing; skipping migrations"
    fi
fi

php artisan optimize || true
EOF

EXPOSE 80

# Base image will start Nginx + PHP-FPM automatically
