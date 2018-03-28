FROM ubuntu:16.04

MAINTAINER Everton Muniz <munizeverton@gmail.com>

RUN apt-get -y update \
    && apt-get -y upgrade \
    && apt-get install -y apache2 php7.0 libapache2-mod-php7.0 php7.0-pgsql php-pear php7.0-curl rpl wget php-xdebug \
    && a2enmod rewrite \
    && apt-get clean

RUN apt-get install -y libreadline6 libreadline6-dev make gcc zlib1g-dev

# Instala pacotes pear
RUN pear install XML_RPC2 Mail Net_SMTP Services_ReCaptcha

ADD ieducar.conf /etc/apache2/sites-available/000-default.conf

RUN echo "xdebug.remote_enable=on" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_autostart=off" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini
RUN echo "xdebug.remote_host=172.17.0.1" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini
RUN echo "xdebug.idekey=PHPSTORM" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini

EXPOSE 80

CMD mkdir /home/portabilis/ieducar
CMD chmod 777 -R /home/portabilis/ieducar
WORKDIR /home/portabilis/ieducar

# Instala dependencia relatórios
RUN apt-get install -y python-software-properties \
    && apt-get -y install software-properties-common \
  	&& add-apt-repository -y ppa:openjdk-r/ppa \
	&& apt-get -y update \
	&& apt-get -y install openjdk-7-jre
CMD update-alternatives --config java

CMD chmod 777 /home/portabilis/ieducar/modules/Reports/ReportSources/Portabilis/

CMD service apache2 restart

CMD /usr/sbin/apache2ctl -D FOREGROUND