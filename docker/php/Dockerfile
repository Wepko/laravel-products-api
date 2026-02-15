FROM php:8.3-fpm

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    default-mysql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Установка расширений - ВАЖНО: pdo ДОЛЖЕН быть перед pdo_mysql!
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets

# Явное включение расширений
RUN docker-php-ext-enable pdo_mysql pdo_pgsql

# Проверка
RUN php -m | grep pdo_mysql || (echo "✗ pdo_mysql not enabled" && exit 1)

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Создание пользователя для Laravel
RUN groupadd -g 1000 laravel && \
    useradd -u 1000 -ms /bin/bash -g laravel laravel

# Установка прав
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache && \
    chown -R laravel:laravel /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Финальная проверка
RUN echo "\n=== PHP Extensions ===" && \
    php -m | grep -E "pdo|pdo_mysql|redis" || true