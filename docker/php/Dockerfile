FROM php:7.3-fpm

LABEL maintainer="Portabilis <contato@portabilis.com.br>"

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_PROCESS_TIMEOUT 900
ENV COMPOSER_DISABLE_XDEBUG_WARN=1

ENV XDEBUG_IDEKEY xdebug
ENV XDEBUG_REMOTE_HOST 127.0.0.1
ENV XDEBUG_REMOTE_PORT 9000
ENV XDEBUG_REMOTE_ENABLE 0
ENV XDEBUG_AUTOSTART 0

RUN echo "deb http://ftp.us.debian.org/debian sid main" >> /etc/apt/sources.list

RUN apt-get update -y

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apt-get install -y libpq-dev
RUN docker-php-ext-install pgsql

RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_pgsql

RUN pecl install redis
RUN docker-php-ext-enable redis
RUN rm -rf /tmp/pear

RUN docker-php-ext-install bcmath

RUN apt-get install -y unzip

RUN apt-get install -y libzip-dev
RUN docker-php-ext-install zip

RUN mkdir -p /usr/share/man/man7
RUN apt-get install -y postgresql-client

RUN apt-get install -y libpng-dev
RUN docker-php-ext-install gd

RUN apt-get install -y jq git

RUN ln -s /var/www/ieducar/artisan /usr/local/bin/artisan

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY php.ini /usr/local/etc/php/php.ini

COPY pdflib.so /usr/local/lib/php/extensions/no-debug-non-zts-20180731/pdflib.so
RUN echo "extension=/usr/local/lib/php/extensions/no-debug-non-zts-20180731/pdflib.so" > /usr/local/etc/php/conf.d/pdflib.ini

RUN docker-php-ext-install pcntl

RUN apt-get install openjdk-8-jdk -y
