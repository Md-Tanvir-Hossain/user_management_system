FROM dunglas/frankenphp:php8.4-bookworm

RUN install-php-extensions \
    pdo_pgsql \
    intl \
    opcache \
    zip

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

RUN php bin/console cache:clear --env=prod

EXPOSE 80