FROM php:8.1-fpm-alpine

LABEL maintainer="Portabilis <contato@portabilis.com.br>"

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_PROCESS_TIMEOUT 900
ENV COMPOSER_DISABLE_XDEBUG_WARN=1

ENV XDEBUG_IDEKEY xdebug
ENV XDEBUG_CLIENT_HOST 127.0.0.1
ENV XDEBUG_CLIENT_PORT 9003
ENV XDEBUG_MODE off
ENV XDEBUG_START_WITH_REQUEST off

COPY locale /etc/default/locale
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN apk add --update \
    libc-dev \
    libpng-dev  \
    libzip-dev  \
    linux-headers \
    openjdk8  \
    postgresql14-client \
    postgresql-dev \
    ttf-dejavu \
    unzip

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
    zip

# https://github.com/docker-library/php/issues/436#issuecomment-303171390
RUN apk del .phpize_deps

RUN ln -s /var/www/ieducar/artisan /usr/local/bin/artisan

ENV COMPOSER_VERSION=2.5.1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version="$COMPOSER_VERSION"
