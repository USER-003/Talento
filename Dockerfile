# Multi-stage Dockerfile for deploying Laravel 11 on Koyeb

# 1) Build frontend assets with Vite
FROM node:20-alpine AS build-assets
WORKDIR /app

# Install frontend dependencies
COPY package*.json ./
RUN npm ci || npm install

# Copy Vite config and sources; build to public/build
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build


# 2) PHP + Nginx runtime image
# Use PHP 8.2 to match Laravel 11 requirements
FROM webdevops/php-nginx:8.2-alpine

# Install system libs required for common Laravel PHP extensions
RUN apk add --no-cache libzip-dev oniguruma-dev postgresql-dev

# Install PHP extensions commonly required by Laravel
RUN docker-php-ext-install \
    bcmath \
    mbstring \
    pdo_mysql \
    pdo_pgsql \
    zip

# Copy Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configure Nginx document root and app env
ENV WEB_DOCUMENT_ROOT=/app/public \
    APP_ENV=production

WORKDIR /app

# Copy application source
COPY . .

# Copy built frontend assets
COPY --from=build-assets /app/public/build ./public/build

# Install PHP dependencies, ensure dir permissions, and (optionally) warm caches
RUN composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader \
    && mkdir -p storage/framework/{cache,sessions,views} \
    && chown -R application:application storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && php artisan config:cache || true \
    && php artisan view:cache || true \
    && php artisan event:cache || true \
    && php artisan route:cache || true \
    && php artisan storage:link || true

# Expose the default Nginx port used by the base image
EXPOSE 80

# The base image starts Nginx + PHP-FPM automatically
