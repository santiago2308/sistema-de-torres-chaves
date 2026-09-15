FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
        libpq \
        postgresql-dev \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        zip \
        git \
    && docker-php-ext-install pdo_pgsql pgsql opcache pcntl bcmath intl zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Config de producción (no mostrar errores, opcache para rendimiento)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

COPY backend/composer.json backend/composer.lock /var/www/html/
COPY backend /var/www/html

# Instalar dependencias --no-scripts evita fallos por falta de .env durante el build
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts \
    && php artisan package:discover --ansi || true; \
    php artisan config:clear

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]