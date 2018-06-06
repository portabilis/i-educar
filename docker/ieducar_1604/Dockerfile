FROM ubuntu:18.04

LABEL maintainer="Caroline Salib <caroline@portabilis.com.br>, Everton Muniz <everton@portabilis.com.br>, Éber Dias <eber@portabilis.com.br>"

ENV DEBIAN_FRONTEND=noninteractive

COPY . /var/www/html/i-educar/

	&& apt-get -y update \
	&& apt-get install -y --no-install-recommends \
	apache2 \
	curl \
	gcc \
	git-core \
	libapache2-mod-php \
	libreadline7 \
	make \
	openjdk-8-jdk \
	php-curl \
	php-mbstring \
	php-pear \
	php-pgsql \
	rpl \
	wget \
	zlib1g-dev \
	software-properties-common \
	&& add-apt-repository -y ppa:openjdk-r/ppa \
	&& a2enmod rewrite \
	&& pear install XML_RPC2 Mail Net_SMTP Services_ReCaptcha \
	&& chown -R www-data:www-data /var/www \
	&& echo "xdebug.remote_enable=on" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini \
	&& echo "xdebug.remote_autostart=off" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini \
	&& echo "xdebug.remote_host=172.17.0.1" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini \
	&& echo "xdebug.idekey=PHPSTORM" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini \
	&& php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
	&& php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
	&& php composer-setup.php \
	&& php -r "unlink('composer-setup.php');" \
	&& mv composer.phar /usr/local/bin/composer \
	&& apt-get clean \
	&& apt-get purge --auto-remove -y \
	&& rm -rf /var/lib/apt/lists/*

COPY ieducar.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

COPY apache2-foreground /usr/local/bin/

CMD ["apache2-foreground"]
