version=`pgvm list | grep -o 8.2.[0-9][0-9]`

pgvm use $version
pgvm cluster $1 main
