FROM php:7.2-fpm

MAINTAINER Eder Soares

RUN apt-get update -y

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apt-get install -y libpq-dev
RUN docker-php-ext-install pgsql

RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install pdo_pgsql

RUN pecl install redis
RUN docker-php-ext-enable redis
RUN rm -rf /tmp/pear

ENV XDEBUG_IDEKEY xdebug
ENV XDEBUG_REMOTE_HOST 127.0.0.1
ENV XDEBUG_REMOTE_PORT 9000
ENV XDEBUG_REMOTE_ENABLE 0
ENV XDEBUG_AUTOSTART 0

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY php.ini /usr/local/etc/php/php.ini

RUN mkdir -p /usr/share/man/man1

RUN apt-get install -y openjdk-8-jre

RUN docker-php-ext-install bcmath

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '48e3236262b34d30969dca3c37281b3b4bbe3221bda826ac6a9a62d6444cdb0dcd0615698a5cbe587c3f0fe57a54d8f5') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"

RUN apt-get install -y zlib1g-dev
RUN docker-php-ext-install zip

RUN apt-get install -y unzip

RUN ln -s /var/www/ieducar/artisan /usr/local/bin/artisan

RUN mkdir -p /usr/share/man/man7
RUN apt-get install -y postgresql-client

RUN apt-get install -y libpng-dev
RUN docker-php-ext-install gd

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_PROCESS_TIMEOUT 900
ENV COMPOSER_DISABLE_XDEBUG_WARN=1
