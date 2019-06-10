FROM nginx

LABEL maintainer="Portabilis <contato@portabilis.com.br>"

RUN apt-get update -y

COPY default.conf /etc/nginx/conf.d/default.conf
COPY upstream.conf /etc/nginx/conf.d/upstream.conf
