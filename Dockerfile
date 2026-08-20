FROM dunglas/frankenphp:php8.4-bookworm

RUN install-php-extensions \
    pdo_pgsql \
    intl \
    opcache \
    zip

RUN apt-get update \
    && apt-get install -y unzip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

COPY . .

RUN php bin/console asset-map:compile --env=prod

RUN php bin/console cache:clear --env=prod

EXPOSE 8000

CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]