# FROM dunglas/frankenphp:php8.4-bookworm

# RUN install-php-extensions \
#     pdo_pgsql \
#     intl \
#     opcache \
#     zip

# RUN apt-get update \
#     && apt-get install -y unzip \
#     && rm -rf /var/lib/apt/lists/*

# WORKDIR /app

# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# COPY . .

# ENV APP_ENV=prod
# ENV APP_DEBUG=0

# RUN composer install \
#     --no-dev \
#     --no-interaction \
#     --prefer-dist \
#     --optimize-autoloader \
#     --no-scripts

# RUN php bin/console cache:clear --env=prod

# EXPOSE 10000

# CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]


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

COPY . .

# Use our Caddy configuration
COPY Caddyfile /etc/caddy/Caddyfile

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

RUN php bin/console cache:clear --env=prod

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]