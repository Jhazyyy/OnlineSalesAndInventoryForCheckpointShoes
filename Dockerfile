# Multi-stage Dockerfile for Laravel 12 on PHP 8.2 with Apache
# - Installs required PHP extensions for this project: zip, gd, pdo_mysql, pdo_pgsql
# - Builds Composer dependencies
# - Builds Vite assets
# - Serves app from public/ via Apache

# 1) Composer dependencies
FROM composer:2 AS composer
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader

# 2) Build frontend assets
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* pnpm-lock.yaml* yarn.lock* ./
RUN if [ -f package-lock.json ]; then npm ci; elif [ -f yarn.lock ]; then yarn install --frozen-lockfile; elif [ -f pnpm-lock.yaml ]; then npm i -g pnpm@8 && pnpm i --frozen-lockfile; else npm i; fi
COPY resources ./resources
COPY vite.config.js ./vite.config.js
COPY postcss.config.js ./postcss.config.js
COPY tailwind.config.js ./tailwind.config.js
RUN npm run build

# 3) Runtime image with Apache + PHP 8.2
FROM php:8.2-apache

# Set working dir
WORKDIR /var/www/html

# Install system deps and PHP extensions needed by the app
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libzip-dev zlib1g-dev \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd zip pdo pdo_mysql pdo_pgsql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Copy project files
COPY . /var/www/html

# Copy Composer vendor from builder
COPY --from=composer /app/vendor /var/www/html/vendor

# Copy built assets
COPY --from=assets /app/public/build /var/www/html/public/build

# Use production PHP settings
ENV APP_ENV=production \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0

# Set proper ownership for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Apache to serve from public/
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Ensure Laravel symlink exists; ignore if already present
RUN php artisan storage:link || true \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache || true

# Expose default Apache port
EXPOSE 80

# Railway sets PORT env; ensure Apache listens on it
ENV PORT=8080
RUN sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf \
    && sed -i "s/:80>/:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Start Apache in foreground
CMD ["apache2-foreground"]
