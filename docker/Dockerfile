FROM php:7.4-fpm-alpine
COPY php.ini /usr/local/etc/php/

RUN apk update && \
    apk add --no-cache icu-dev libzip-dev git zip unzip oniguruma-dev && \
    docker-php-ext-install intl pdo_mysql mbstring zip bcmath opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

WORKDIR /work
