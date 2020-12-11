FROM php:7.3-fpm-alpine3.11

LABEL maintainer="Portabilis <contato@portabilis.com.br>"

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_PROCESS_TIMEOUT 900
ENV COMPOSER_DISABLE_XDEBUG_WARN=1

ENV XDEBUG_IDEKEY xdebug
ENV XDEBUG_REMOTE_HOST 127.0.0.1
ENV XDEBUG_REMOTE_PORT 9000
ENV XDEBUG_REMOTE_ENABLE 0
ENV XDEBUG_AUTOSTART 0

RUN apk update
RUN apk add wget

RUN apk add --no-cache --virtual .phpize_deps $PHPIZE_DEPS

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apk --no-cache add postgresql-dev
RUN docker-php-ext-install pgsql

RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_pgsql

RUN pecl install redis
RUN docker-php-ext-enable redis
RUN rm -rf /tmp/pear

RUN docker-php-ext-install bcmath

RUN apk add unzip

RUN apk add libzip-dev
RUN docker-php-ext-install zip

RUN apk add libc-dev

RUN mkdir -p /usr/share/man/man7
RUN echo "@v3.4 http://nl.alpinelinux.org/alpine/v3.4/main" >> /etc/apk/repositories && \
    apk update && \
    apk add --no-cache "postgresql-client@v3.4>=9.5"

RUN apk add libpng-dev
RUN docker-php-ext-install gd

RUN apk add jq git

RUN ln -s /var/www/ieducar/artisan /usr/local/bin/artisan

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY php.ini /usr/local/etc/php/php.ini

COPY pdflib.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/pdflib.so
RUN echo "extension=/usr/local/lib/php/extensions/no-debug-non-zts-20180731/pdflib.so" > /usr/local/etc/php/conf.d/pdflib.ini

RUN docker-php-ext-install pcntl

RUN apk add openjdk8

RUN apk add --update npm

RUN composer self-update --1

RUN apk del .phpize_deps
