FROM ubuntu:16.04

LABEL maintainer="Caroline Salib <caroline@portabilis.com.br>, Everton Muniz <everton@portabilis.com.br>, Éber Dias <eber@portabilis.com.br>"

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update \
    && apt-get install -y --no-install-recommends \
        apache2 \
        php7.0 \
        libapache2-mod-php7.0 \
        php7.0-pgsql \
        php-pear \
        php-zip \
        php7.0-curl \
        rpl \
        wget \
        git \
        php-xdebug \
        php-mbstring \
        libreadline6 \
        libreadline6-dev \
        make \
        gcc \
        zlib1g-dev \
        openjdk-8-jre \
    && apt-get clean \
    && apt-get purge --auto-remove -y \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

RUN pear install XML_RPC2

COPY ieducar.conf /etc/apache2/sites-available/000-default.conf

RUN echo "xdebug.remote_enable=\${XDEBUG_REMOTE_ENABLE}" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_host=\${XDEBUG_REMOTE_HOST}" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini \
    && echo "xdebug.remote_port=\${XDEBUG_REMOTE_PORT}" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini \
    && echo "xdebug.idekey=\${XDEBUG_IDEKEY}" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

EXPOSE 80

RUN mkdir -p /home/portabilis/ieducar \
    && chmod 777 -R /home/portabilis/ieducar

WORKDIR /home/portabilis/ieducar

COPY apache2-foreground /usr/local/bin/

CMD ["apache2-foreground"]
