#!/bin/bash

apt-get update -y \
&& apt-get install -y --no-install-recommends \
postgresql\
postgresql-contrib \
&& su postgres -c "curl https://raw.githubusercontent.com/portabilis/i-educar/master/docker/postgres_95/init.sql | psql"
