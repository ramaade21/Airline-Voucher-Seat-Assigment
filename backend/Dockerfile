FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libsqlite3-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --no-progress || true

COPY . .

RUN composer install --no-dev --no-interaction --prefer-dist --no-progress \
    && mkdir -p database \
    && touch database/database.sqlite \
    && cp -n .env.example .env || true \
    && php artisan key:generate --force \
    && php artisan migrate --force \
    && php artisan config:cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
