FROM ubuntu:16.04

LABEL maintainer="Caroline Salib <caroline@portabilis.com.br>, Everton Muniz <everton@portabilis.com.br>, Éber Dias <eber@portabilis.com.br>, Fábio Rodrigues Ribeiro<farribeiro@gmail.com>"

ENV DEBIAN_FRONTEND=noninteractive
ENV XDEBUG="/etc/php/7.0/apache2/conf.d/20-xdebug.ini"


RUN apt-get -y update \
	&& apt-get install -y --no-install-recommends \
	apache2 \
	curl \
	gcc \
	git \
	libapache2-mod-php7.0 \
	libreadline6 \
	libreadline6-dev \
	make \
	openjdk-8-jre \
	php-mbstring \
	php-pear \
	php-xdebug \
	php-zip \
	php7.0 \
	php7.0-curl \
	php7.0-pgsql \
	rpl \
	software-properties-common \
	wget \
	zlib1g-dev \
	&& wget https://github.com/portabilis/i-educar/archive/master.tar.gz \
	&& tar zxf master.tar.gz \
	&& rm -rf master.tar.gz \
	&& mv i-educar-master/* /var/www \
	&& rm -rf master.tar.gz \
	&& wget https://github.com/portabilis/i-educar-reports-package/archive/master.tar.gz \
	&& rm -rf master.tar.gz i-educar-master \
	&& find /var/www/ -type f -exec chmod 644 {} \; \
	&& find /var/www/ -type d -exec chmod 755 {} \; \
	&& cp -a /var/www/docker/ieducar_1604/ieducar.conf /etc/apache2/sites-available/000-default.conf \
	&& cp -a /var/www/docker/ieducar_1604/apache-foreground /usr/local/bin \
	&& cp -a /var/www/ieducar/configuration/ieducar.ini.sample /var/www/ieducar/configuration/ieducar.ini.sample \
	&& chmod 744 /usr/local/bin/apache_foreground \
	&& a2enmod rewrite \
	&& pear install XML_RPC2 Mail Net_SMTP Services_ReCaptcha \
	&& chown -R www-data:www-data /var/www \
	&& echo "xdebug.remote_enable=on" >> ${XDEBUG} \
	&& echo "xdebug.remote_autostart=off" >> ${XDEBUG} \
	&& echo "xdebug.remote_host=172.17.0.1" >> ${XDEBUG} \
	&& echo "xdebug.idekey=PHPSTORM" >> ${XDEBUG} \
	&& php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
	&& php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
	&& php composer-setup.php \
	&& php -r "unlink('composer-setup.php');" \
	&& mv composer.phar /usr/local/bin/composer \
	&& composer install \
	&& apt-get clean \
	&& apt-get purge --auto-remove -y \
	&& rm -rf /var/lib/apt/lists/*

EXPOSE 80

CMD ["apache2-foreground"]
