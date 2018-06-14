FROM postgres:9

LABEL maintainer="Fábio Rodrigues Ribeiro <farribeiro@gmail.com>"

VOLUME /var/lib/postgresql/data
VOLUME ./init.sql /docker-entrypoint-initdb.d/00-init.sql\
