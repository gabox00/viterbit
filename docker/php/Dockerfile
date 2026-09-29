FROM dunglas/frankenphp:1-php8.5

RUN install-php-extensions amqp intl opcache pdo_pgsql pcov zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/Caddyfile /etc/frankenphp/Caddyfile
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    SERVER_NAME=:8000

WORKDIR /app
