FROM php:7.0-apache

LABEL maintainer="Caroline Salib <caroline@portabilis.com.br>, Everton Muniz <everton@portabilis.com.br>, Éber Dias <eber@portabilis.com.br>, Fábio Rodrigues Ribeiro<farribeiro@gmail.com>"

ENV DEBIAN_FRONTEND=noninteractive
ENV XDEBUG="/etc/php/7.0/apache2/conf.d/20-xdebug.ini"

RUN apt-get -y update \
	&& apt-get install -y --no-install-recommends \
	curl \
	gcc \
	git \
	make \
	rpl \
	software-properties-common \
	unzip \
	wget \
	zip \
	zlib1g-dev \
	&& wget https://github.com/portabilis/i-educar/archive/master.tar.gz \
	&& tar zxf master.tar.gz -C /var/www/html \
	&& mv /var/www/html/i-educar-master /var/www/html/i-educar \
	&& rm -rf master.tar.gz \
	&& mkdir -p /var/www/html/i-educar/ieducar/modules/Reports \
	&& wget https://github.com/portabilis/i-educar-reports-package/archive/master.tar.gz \
	&& tar zxf master.tar.gz -C /var/www/html \
	&& mv /var/www/html/i-educar-reports-package-master/* /var/www/html/i-educar/ieducar/modules/Reports \
	&& rm -rf master.tar.gz i-educar-reports-package-master \
	&& find /var/www/ -type f -exec chmod 644 {} \; \
	&& find /var/www/ -type d -exec chmod 755 {} \; \
	&& cp -a /var/www/html/i-educar/docker/ieducar_1604/ieducar.conf /etc/apache2/sites-available/000-default.conf \
	&& a2enmod rewrite \
	&& pear install XML_RPC2 Mail Net_SMTP Services_ReCaptcha \
	&& chown -R www-data:www-data /var/www \
	&& php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
	&& php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
	&& php composer-setup.php \
	&& php -r "unlink('composer-setup.php');" \
	&& mv composer.phar /usr/local/bin/composer \
	&& composer install \
	&& apt-get clean \
	&& apt-get purge --auto-remove -y \
	&& rm -rf /var/lib/apt/lists/*
