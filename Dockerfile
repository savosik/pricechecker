FROM php:8.4-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && docker-php-ext-enable pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && mkdir -p /var/log/supervisor

# Установка Redis расширения
RUN pecl install redis && docker-php-ext-enable redis

# Установка OPcache
RUN docker-php-ext-install opcache && docker-php-ext-enable opcache

# Копирование PHP конфигурации
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/php-custom.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создание рабочей директории
WORKDIR /var/www/html

# Копирование файлов приложения
COPY . /var/www/html

# Установка прав доступа
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Копирование конфигурации Supervisor
COPY docker/php/supervisor.conf /etc/supervisor/conf.d/laravel-worker.conf

# Установка зависимостей (если composer.json существует)
RUN if [ -f "composer.json" ]; then composer install --no-interaction --prefer-dist --optimize-autoloader; fi

EXPOSE 9000

CMD ["php-fpm"]
