#!/bin/sh -e

# Lista dos tenant no qual já foram executado as migrations de setup
NEW_TENANTYS="escola.municipio1 escola.municipio2"

# Lista dos tenant no qual já foram executado as migrations de setup
TENANTYS=""

function executeNewInstall {
	echo "Definindo Tenant: $TENANT"
	#sed -i "s/DB_CONNECTION=pgsql/DB_CONNECTION=$TENANT/g" .env
	sleep 2s
	echo "Preparando para executar as migrations com seeds para o novo Tenant: $TENANT"
	DB_CONNECTION=$TENANT php artisan migrate --seed --force --ansi
}

function executeUpdateInstall {
	echo "Definindo Tenant: $TENANT"
	#sed -i "s/DB_CONNECTION=pgsql/DB_CONNECTION=$TENANT/g" .env
	sleep 2s
	echo "Preparando para executar as migrations sem seeds para o Tenant: $TENANT"
	DB_CONNECTION=$TENANT php artisan migrate --force --ansi
}

# Laço de repetição novos tenants
for TENANT in $NEW_TENANTYS; do
  executeNewInstall $TENANT
  #sed -i "s/DB_CONNECTION=$TENANT/DB_CONNECTION=pgsql/g" .env
done

# Laço de repetição tenants existentes
for TENANT in $TENANTYS; do
  executeUpdateInstall $TENANT
  #sed -i "s/DB_CONNECTION=$TENANT/DB_CONNECTION=pgsql/g" .env
done
