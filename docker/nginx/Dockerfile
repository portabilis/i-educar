FROM nginx:alpine

LABEL maintainer="Portabilis <contato@portabilis.com.br>"

RUN apk update

COPY app.conf /etc/nginx/conf.d/app.conf
COPY empty.conf /etc/nginx/conf.d/default.conf
COPY upstream.conf /etc/nginx/conf.d/upstream.conf

COPY ssl/cert.pem /etc/nginx/ssl/cert.pem
COPY ssl/cert.key /etc/nginx/ssl/cert.key