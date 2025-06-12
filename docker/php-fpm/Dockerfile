# Stage 1: Build dependencies với Composer
FROM composer:2 AS build

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-progress --prefer-dist

# Stage 2: Final image để chạy Laravel
FROM php:8.4-fpm

# Cài PHP extension
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libonig-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

# Copy Composer từ build stage
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN groupmod -g 1000 www-data && \
    usermod -u 1001 -g 0 www-data
# Copy vendor từ build stage
COPY --from=build /app/vendor /var/www/vendor

# Tạo thư mục làm việc
WORKDIR /var/www

# Copy toàn bộ source code (trừ vendor đã copy riêng)
COPY . .

# Cấp quyền cho Laravel
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache
