FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libzip-dev \
        libonig-dev \
        zip \
    && docker-php-ext-install pdo pdo_mysql opcache \
    && rm -rf /var/lib/apt/lists/*

# Enable OPcache + realpath cache tuning for fast requests on bind mounts.
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN chown -R www-data:www-data /var/www/html

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
