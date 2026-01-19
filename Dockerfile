FROM php:8.3-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    default-mysql-client \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
    libpq-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    sockets

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка Redis расширения
RUN pecl install redis && docker-php-ext-enable redis

# Установка Elasticsearch расширения
RUN pecl install elasticsearch && docker-php-ext-enable elasticsearch

# Установка Node.js для фронтенд-зависимостей
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Настройка рабочей директории
WORKDIR /var/www/html

# Копирование кода приложения
COPY . .

# Смена владельца файлов
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Установка зависимостей Laravel
RUN composer install --no-interaction --optimize-autoloader \
    && npm install

# Создание пользователя для Laravel
RUN useradd -G www-data,root -u 1000 -d /home/laravel laravel \
    && mkdir -p /home/laravel/.composer \
    && chown -R laravel:laravel /home/laravel

USER laravel

EXPOSE 9000

CMD ["php-fpm"]