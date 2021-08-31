FROM nginx:alpine

LABEL maintainer="Portabilis <contato@portabilis.com.br>"

RUN apk update

RUN mv /etc/nginx/nginx.conf /etc/nginx/nginx.conf.ori
COPY nginx.conf /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/conf.d/default.conf
COPY upstream.conf /etc/nginx/conf.d/upstream.conf
