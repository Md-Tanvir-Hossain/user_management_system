FROM php:8.4-cli-bookworm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    intl \
    zip \
    opcache \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

RUN APP_ENV=prod \
    APP_DEBUG=0 \
    APP_SECRET=build-secret \
    APP_DEFAULT_URI=http://127.0.0.1 \
    php bin/console importmap:install --env=prod

RUN APP_ENV=prod \
    APP_DEBUG=0 \
    APP_SECRET=build-secret \
    APP_DEFAULT_URI=http://127.0.0.1 \
    php bin/console cache:clear --env=prod

EXPOSE 80

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-80} -t public"]