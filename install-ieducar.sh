#!/bin/bash

<<LICENSE
Copyright (c) 2016 Caroline Salib.

--

Este programa é um software livre; você pode redistribuí-lo e/ou
modificá-lo dentro dos termos da Licença Pública Geral GNU como
publicada pela Fundação do Software Livre (FSF); na versão 3 da
Licença, ou (na sua opinião) qualquer versão.

Este programa é distribuído na esperança de que possa ser útil,
mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO
a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
Licença Pública Geral GNU para maiores detalhes.

Você deve ter recebido uma cópia da Licença Pública Geral GNU junto
com este programa. Se não, veja <http://www.gnu.org/licenses/>.
LICENSE

echo -e '\n\n  Bem vindo a instalação do i-Educar.'
echo -e '\n  Este script lhe guiará na instalação do software, para mais detalhes acesse a página oficial da comunidade i-Educar https://portal.softwarepublico.gov.br/social/i-educar/'

exit_if_failed () {
  if [ $1 = 0 ]; then
    return 0;
  fi

  echo -e "\n\n\n  Desculpe, mas a instalação não pode continuar pois ocorreu algum erro inesperado."
  echo -e "\n  Por favor, tente novamente."

  exit 1;
}

required_read () {
  echo -n "$1"
  read _INPUT

  if [  -z "$_INPUT" ]; then
    required_read "$1"
  fi
}

login_or_create_user () {
  echo -e "\n  A instalação não pode ser realizada pelo usuário root, selecione uma opção:\n"
  echo -e '    1 - logar-se com outro usuário'
  echo -e '    2 - criar um novo usuário\n'

  required_read '    opção: '

  if [ $_INPUT = 1 ]; then
    echo -e '\n'
    required_read '    informe o nome do seu usuário: '

    su $_INPUT $0
    exit 0

  elif [[ $_INPUT = 2 ]]; then
    echo -e '\n'
    required_read '    informe o nome do novo usuário (ex: ieducar): '

    useradd --create-home --groups sudo --shell /bin/bash $_INPUT
    exit_if_failed $?

    echo -e '\n    por favor, informe a senha do novo usuário:\n'
    sudo passwd $_INPUT
    exit_if_failed $?

    su $_INPUT $0
    exit 0
  else
    echo -e '\n'
    echo -n '    opção inválida, tente novamente.'
    read -n 1

    login_or_create_user
  fi
}

install_packages () {
  echo -e '\n  * instalando dependências\n'

  # fix "Failed to fetch bzip2 ... Hash Sum mismatch" error on apt-get update
  sudo rm -rf /var/lib/apt/lists/*

  sudo apt-get update -y
  sudo apt-get install -y curl wget rpl unzip
  exit_if_failed $?

  # pg
  sudo apt-get install -y libreadline6 libreadline6-dev make gcc zlib1g-dev flex bison
  exit_if_failed $?
}

install_apache () {
  echo -e '\n\n  * instalando apache\n'
  sudo apt-get install -y apache2
  exit_if_failed $?

  sudo a2enmod rewrite
  exit_if_failed $?

  sudo service apache2 restart
  exit_if_failed $?
}

install_php () {
  echo -e '\n\n  * instalando php\n'
  sudo apt-get install -y libapache2-mod-php5 php5-pgsql php5-curl
  exit_if_failed $?
}

install_pear () {
  echo -e '\n\n  * instalando pear\n'
  sudo apt-get install -y php-pear
  exit_if_failed $?

  sudo service apache2 restart
  exit_if_failed $?
}

install_pgvm () {
  echo -e '\n\n  * instalando pgvm\n'
  curl -s -L https://raw.github.com/guedes/pgvm/master/bin/pgvm-self-install | bash -s -- --update
  exit_if_failed $?

  #source ~/.bashrc

  if [ -z "$pgvm_home" ]
  then
    pgvm_home=/home/$USER/.pgvm
    pgvm_logs=${pgvm_home}/logs
    pgvm_clusters=${pgvm_home}/clusters
    pgvm_environments=${pgvm_home}/environments

    export pgvm_home pgvm_logs pgvm_environments pgvm_clusters

    export PATH=${pgvm_home}/bin:$PATH
    export PATH=${pgvm_environments}/current/bin:$PATH
  fi
}

install_pg () {
  echo -e '\n\n  * instalando postgres 8.2 via pgvm\n'
  pgvm install 8.2.23
  pgvm use 8.2.23
  pgvm cluster create main
  pgvm cluster start main

  echo -e '\n'
  required_read '    informe o nome desejado para o banco de dados (ex: ieducar): '
  DBNAME=$_INPUT

  echo -e '\n\n  * destruindo banco de dados caso exista\n'
  ~/.pgvm/environments/8.2.23/bin/dropdb $DBNAME -p 5433

  DBUSER=ieducar

  if [ $USER != $DBUSER ]; then
    echo -e '\n\n  * criando usuário do banco de dados\n'
    ~/.pgvm/environments/8.2.23/bin/psql -d postgres -p 5433 -c "DROP USER IF EXISTS $DBUSER;"
    ~/.pgvm/environments/8.2.23/bin/createuser --superuser $DBUSER -p 5433
    exit_if_failed $?
  fi

  echo -e '\n\n  * baixando dump banco de dados\n'
  rm -f ieducar.backup.gz
  rm -f ieducar.backup
  wget https://s3-us-west-2.amazonaws.com/portabilis2/public/ieducar/ieducar.backup.gz
  gunzip ieducar.backup.gz
  exit_if_failed $?

  echo -e '\n\n * criando banco de dados\n'
  ~/.pgvm/environments/8.2.23/bin/createdb $DBNAME -E latin1 -p 5433
  exit_if_failed $?

  echo -e '\n\n * restaurando dump do banco de dados\n'
  ~/.pgvm/environments/8.2.23/bin/pg_restore -d $DBNAME -p 5433 -U $DBUSER --no-owner ieducar.backup
  #exit_if_failed $?

  rm -f ieducar.backup.gz
  rm -f ieducar.backup

  echo -e '\n\n * definindo search_path\n'
  ~/.pgvm/environments/8.2.23/bin/psql -d $DBNAME -p 5433 -c 'ALTER DATABASE '$DBNAME' SET search_path = "$user", public, portal, cadastro, acesso, alimentos, consistenciacao, historico, pmiacoes, pmicontrolesis, pmidrh, pmieducar, pmiotopic, urbano, modules;'
  exit_if_failed $?
}

install_git () {
  echo -e '\n\n  * instalando git\n'
  sudo apt-get install -y git-core
  exit_if_failed $?
}

clone_ieducar () {
  echo -e '\n'
  required_read '    informe o nome do diretório em que a aplicação será instalada (ex: ieducar): '
  APPDIR=$_INPUT

  echo -e '\n\n  * destruindo repositório ieducar local caso exista\n'
  rm -rf $APPDIR

  echo -e "\n\n  * clonando repositório ieducar no caminho $HOME/$APPDIR\n"
  git -c http.sslVerify=false clone http://softwarepublico.gov.br/gitlab/i-educar/i-educar.git $APPDIR
  exit_if_failed $?

  echo -e "\n\n  * reconfigurando ieducar\n"
  rpl "app.database.dbname   = ieducar" "app.database.dbname   = $DBNAME" $APPDIR/ieducar/configuration/ieducar.ini
  sudo service apache2 reload
}

install_ieducar_packages () {
  echo -e '\n\n  * instalando dependências i-Educar via pear\n'

  # pear download is freezing :(
  #bash ~/ieducar/ieducar/scripts/install_pear_packages.sh

  wget http://download.pear.php.net/package/Mail-1.2.0.tgz
  wget http://download.pear.php.net/package/Net_Socket-1.0.14.tgz
  wget http://download.pear.php.net/package/Net_SMTP-1.6.2.tgz
  wget http://download.pear.php.net/package/Net_URL2-2.0.5.tgz
  wget http://download.pear.php.net/package/HTTP_Request2-2.2.0.tgz
  wget http://download.pear.php.net/package/Services_ReCaptcha-1.0.3.tgz

  sudo pear install -O Mail-1.2.0.tgz
  exit_if_failed $?

  sudo pear install -O Net_Socket-1.0.14.tgz
  exit_if_failed $?

  sudo pear install -O Net_SMTP-1.6.2.tgz
  exit_if_failed $?

  sudo pear install -O Net_URL2-2.0.5.tgz
  exit_if_failed $?

  sudo pear install -O HTTP_Request2-2.2.0.tgz
  exit_if_failed $?

  sudo pear install -O Services_ReCaptcha-1.0.3.tgz
  exit_if_failed $?

  rm Mail-1.2.0.tgz
  rm Net_Socket-1.0.14.tgz
  rm Net_SMTP-1.6.2.tgz
  rm Net_URL2-2.0.5.tgz
  rm HTTP_Request2-2.2.0.tgz
  rm Services_ReCaptcha-1.0.3.tgz

  sudo service apache2 restart
}

config_apache () {
  echo -e '\n\n  * configurando virtual host apache\n'
  sudo rm -f /etc/apache2/sites-enabled/ieducar
  sudo rm -f /etc/apache2/sites-available/ieducar
  sudo rm -f /etc/apache2/sites-available/apache-sites-available-ieducar

  sudo wget https://s3-us-west-2.amazonaws.com/portabilis2/public/ieducar/apache/sites-available/ieducar -P /etc/apache2/sites-available/
  sudo mv /etc/apache2/sites-available/apache-sites-available-ieducar /etc/apache2/sites-available/ieducar

  echo -e "\n\n  * reconfigurando virtual host\n"
  sudo rpl "/home/portabilis/ieducar" "$HOME/$APPDIR" /etc/apache2/sites-available/ieducar
  sudo service apache2 reload

  sudo a2dissite 000-default
  sudo a2ensite ieducar
  sudo service apache2 restart

  if [ $ISSERVER = 0 ]; then
    echo -e '\n'
    required_read '    informe o host desejado para acesso local ao sistema no navegador (ex: ieducar.local): '
    HOST=$_INPUT

    if ! grep -q $HOST /etc/hosts; then
      echo -e '\n\n * adicionando host para $HOST\n'
      echo "127.0.0.1   $HOST" | sudo tee -a /etc/hosts
    fi
  fi
}

add_crontab_job () {
  echo -e '\n\n  * instalando job crontab para inicializar o banco de dados ao iniciar o servidor\n'

  crontab -l > tmp_crontab
  echo "@reboot $HOME/.pgvm/environments/8.2.23/bin/postgres -D $HOME/.pgvm/clusters/8.2.23/main" >> tmp_crontab

  crontab tmp_crontab
  rm tmp_crontab
}

before_install () {
  dpkg -l ubuntu-desktop >/dev/null 2>/dev/null
  ISSERVER=$? # ! desktop
}

install () {
  cd ~

  before_install
  install_packages
  install_apache
  install_php
  install_pear
  install_git
  install_pgvm
  install_pg
  clone_ieducar
  install_ieducar_packages
  config_apache
  add_crontab_job

  echo -e '\n\n  --------------------------------------------'
  echo -e "\n  Parabéns o i-Educar foi instalado com sucesso,"

  server_ip=$(ifconfig | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p')

  if [ $ISSERVER = 0 ]; then
    echo -e "  você pode acessar o sistema em http://$HOST/ (neste computador) ou em http://$server_ip/ nos demais computadores da rede local."
  else
    echo -e "  você pode acessar o sistema em http://$server_ip/"
  fi

  echo -e "\n  utilize usuário admin e senha admin"

  echo -e "\n\n  * Tire suas dúvidas e receba por e-mail as novidades e atualizações do i-Educar, assinando a lista de e-mails da comunidade: https://softwarepublico.gov.br/gitlab/i-educar/i-educar/wikis/como-se-inscrever-e-participar-das-listas"

}

if [ $USER = 'root' ]; then
  login_or_create_user
else
  echo -e "\n\n  instalando i-Educar com usuário $USER"
	install
fi