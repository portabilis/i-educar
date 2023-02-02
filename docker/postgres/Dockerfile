FROM postgres:14-alpine

LABEL maintainer="Portabilis <contato@portabilis.com.br>"

RUN mkdir -p /etc/postgresql
COPY pg_hba.conf /etc/postgresql/pg_hba.conf
COPY postgresql.conf /etc/postgresql/postgresql.conf

CMD ["postgres", "-c", "config_file=/etc/postgresql/postgresql.conf"]