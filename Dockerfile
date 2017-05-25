FROM ubuntu:16.04

MAINTAINER Caroline Salib <caroline@portabilis.com.br>

RUN apt-get -y update \
    && apt-get install -y curl php-curl git-core apache2 libapache2-mod-php php-pgsql php-pear php-mbstring rpl wget \
    && a2enmod rewrite \
    && apt-get clean

RUN apt-get install -y libreadline6 libreadline6-dev make gcc zlib1g-dev

# Instala pacotes pear
RUN pear install XML_RPC2 Mail Net_SMTP Services_ReCaptcha

ADD ieducar.conf /etc/apache2/sites-available/000-default.conf
CMD a2ensite 000-default.conf

EXPOSE 80

CMD mkdir /var/www/html/i-educar
CMD chmod 777 -R /var/www/html/i-educar
WORKDIR /var/www/html/i-educar

CMD /usr/sbin/apache2ctl -D FOREGROUND

