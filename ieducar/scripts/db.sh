if [ -z "$pgvm_home" ]
then
  export pgvm_home=$HOME/.pgvm
  export PATH=$pgvm_home/bin:$PATH
  export PATH=$pgvm_home/environments/current/bin:$PATH
fi

version=`pgvm list | grep -o 8.2.[0-9][0-9]`

pgvm use $version
pgvm cluster $1 main
