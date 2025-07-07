ARG PHP_VERSION=8
ARG COMPOSER_VERSION=latest

FROM composer:${COMPOSER_VERSION} AS composer
FROM php:${PHP_VERSION}-fpm-alpine

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
    postgresql17-client \
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

# Argumentos para UID e GID do usuário do host
# Esses valores devem ser passados pelo docker-compose.yml
# para garantir que os arquivos gerados no volume montado
# tenham o mesmo proprietário do usuário no host (ex: ieducar)
ARG HOST_UID
ARG HOST_GID

# Criação de grupo e usuário com UID e GID iguais ao do host
# Isso garante que arquivos criados dentro do container tenham
# as mesmas permissões do usuário do host (evita problemas com volumes)
RUN addgroup -g ${HOST_GID} ieducar && \
    adduser -u ${HOST_UID} -G ieducar -s /bin/sh -D ieducar

# Define o usuário padrão do container para evitar execução como root
USER ieducar

# Define o diretório de trabalho
WORKDIR /var/www/ieducar