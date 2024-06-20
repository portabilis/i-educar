FROM composer:2.7 as composer
FROM php:8.3-fpm-alpine

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

LABEL maintainer="Portabilis <contato@portabilis.com.br>"

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_PROCESS_TIMEOUT 900
ENV COMPOSER_DISABLE_XDEBUG_WARN=1

ENV XDEBUG_IDEKEY xdebug
ENV XDEBUG_CLIENT_HOST 127.0.0.1
ENV XDEBUG_CLIENT_PORT 9003
ENV XDEBUG_MODE dev,debug,coverage
ENV XDEBUG_START_WITH_REQUEST off

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN apk add --update \
    libc-dev \
    libpng-dev  \
    libzip-dev  \
    linux-headers \
    openjdk8  \
    postgresql16-client \
    postgresql-dev \
    ttf-dejavu \
    unzip \
    openssh \
    git

# https://github.com/docker-library/php/issues/436#issuecomment-303171390
RUN apk add --no-cache --virtual .phpize_deps $PHPIZE_DEPS

RUN pecl install \
    redis \
    xdebug

RUN docker-php-ext-enable \
    redis \
    xdebug

RUN docker-php-ext-install  \
    bcmath \
    gd \
    pcntl \
    pdo \
    pdo_pgsql \
    pgsql \
    zip \
    ftp

RUN docker-php-ext-enable  \
     bcmath \
     gd \
     pcntl \
     pdo \
     pdo_pgsql \
     pgsql \
     zip \
     ftp

# https://github.com/docker-library/php/issues/436#issuecomment-303171390
RUN apk del .phpize_deps

RUN ln -s /var/www/ieducar/artisan /usr/local/bin/artisan
