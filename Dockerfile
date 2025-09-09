FROM php:8.4-fpm-alpine

ARG UID=1000
ARG GID=1000

RUN addgroup -g $GID appgroup && adduser -G appgroup -u $UID -D appuser

WORKDIR /var/www/app

RUN apk update && apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    autoconf \
    gcc \
    g++ \
    make \
    linux-headers \
    $PHPIZE_DEPS

RUN docker-php-ext-install pdo pdo_mysql

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN chmod -R 777 /var/www/app

USER appuser