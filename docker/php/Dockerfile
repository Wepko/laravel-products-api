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
    && docker-php-ext-install \
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

# Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Elasticsearch extension (опционально, но если используете)
# RUN pecl install elasticsearch && docker-php-ext-enable elasticsearch

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Настройка рабочей директории и прав
WORKDIR /var/www

# Создание пользователя для Laravel (опционально, но лучше для безопасности)
RUN groupadd -g 1000 laravel && \
    useradd -u 1000 -ms /bin/bash -g laravel laravel

# Переключение на пользователя laravel№
#USER laravel

# Или оставайтесь root для удобства разработки
# USER root

# Установка Node.js (если нужно для фронтенда)
# RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
#     && apt-get install -y nodejs