FROM ubuntu:12.04

MAINTAINER Caroline Salib <caroline@portabilis.com.br>

RUN apt-get -y update \
    && apt-get -y upgrade \
    && apt-get install -y apache2 php5 libapache2-mod-php5 php5-pgsql php-pear rpl wget \
    && a2enmod rewrite \
    && apt-get clean

RUN apt-get install -y libreadline6 libreadline6-dev make gcc zlib1g-dev

# Instala pacotes pear
RUN pear install XML_RPC2 Mail Net_SMTP Services_ReCaptcha

ADD ieducar.conf /etc/apache2/sites-available/default

EXPOSE 80

CMD mkdir /home/portabilis/ieducar
CMD chmod 777 -R /home/portabilis/ieducar
WORKDIR /home/portabilis/ieducar

# Instala dependencia relatórios
RUN apt-get -y install openjdk-6-jdk
CMD update-alternatives --config java

CMD chmod 777 /home/portabilis/ieducar/modules/Reports/ReportSources/Portabilis/

CMD /usr/sbin/apache2ctl -D FOREGROUND
