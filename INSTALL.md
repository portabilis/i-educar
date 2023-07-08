# Guia de instalação

Você pode instalar o i-Educar diretamente no seu servidor web ou utilizar Docker.

- [Dependências](#dependências)
- [Instalação em servidor web](#instalação-em-servidor-web)
- [Instalação utilizando Docker](#instalação-utilizando-docker)
- [Primeiro acesso](#primeiro-acesso)

## Dependências

Para executar o projeto é necessário a utilização de alguns softwares.

### Servidor

- [PHP](http://php.net/)
- [Composer](https://getcomposer.org/)
- [Nginx](https://www.nginx.com/)
- [Postgres](https://www.postgresql.org/)
- [Redis](https://redis.io/)
- [Git](https://git-scm.com/downloads)

### Docker

- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Instalação em servidor web

Para instalar o projeto execute **todos os passos** abaixo conectado em seu servidor web:

> Este passo a passo é para um servidor Ubuntu 22.04 LTS

Gere uma chave SSH no seu servidor, copie e adicione ao seu GitHub https://github.com/settings/keys.  

```bash
ssh-keygen -t ed25519
cat ~/.ssh/id_ed25519.pub # copie e adicione ao seu GitHub
```

Adicione os repositórios de dependências e sincronize a lista de diretórios:

```bash
add-apt-repository ppa:openjdk-r/ppa -y
add-apt-repository ppa:ondrej/php -y
apt update
```

Instale as dependências:

```bash
apt install -y nginx openjdk-8-jdk postgresql postgresql-contrib openssl unzip php8.2-common php8.2-cli php8.2-fpm php8.2-bcmath php8.2-curl php8.2-mbstring php8.2-pgsql php8.2-xml php8.2-zip php8.2-gd
```

Configure o banco de dados, será solicitado uma senha, utilize `ieducar`:

```bash
systemctl start postgresql.service
sudo -i -u postgres
createuser ieducar --superuser --createdb --pwprompt
# Enter password for new role:
# Enter it again:
```

Crie o banco de dados e volte ao usuário `root`:

```bash 
createdb ieducar
exit
```

Configure o Composer:

```bash 
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php --install-dir=/usr/bin --filename=composer
php -r "unlink('composer-setup.php');"
export COMPOSER_ALLOW_SUPERUSER=1
```

Clone o repositório do i-Educar e copie o arquivo `.env`:

```bash 
git clone https://github.com/portabilis/i-educar.git /var/www/ieducar
cd /var/www/ieducar/
cp /var/www/ieducar/.env.example /var/www/ieducar/.env
```

Copie os arquivos de configuração do Nginx:

```bash 
cp /var/www/ieducar/docker/nginx/default.conf /etc/nginx/conf.d/default.conf
cp /var/www/ieducar/docker/nginx/upstream.conf /etc/nginx/conf.d/upstream.conf
sed -i 's/php:9000/unix:\/run\/php\/php-fpm.sock/g' /etc/nginx/conf.d/upstream.conf
rm /etc/nginx/sites-enabled/default

nginx -t
nginx -s reload

# nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
# nginx: configuration file /etc/nginx/nginx.conf test is successful
```

Faça a instalação do i-Educar:

```bash 
composer new-install
```

Este passo é opcional, mas caso você desejar, você pode popular o banco de dados com alguns dados iniciais utilizando o 
comando abaixo:

```bash 
php artisan db:seed --class=DemoSeeder
```



## Instalação utilizando Docker

> ATENÇÃO: Essa forma de instação tem o objetivo de facilitar demonstrações e
desenvolvimento. Não é recomendado para ambientes de produção!

Para instalar o projeto execute **todos os passos** abaixo, caso você deseje
atualizar sua instalação do i-Educar, siga os passos do [guia de atualização](UPGRADE.md).

Clone o repositório:

```bash
git clone git@github.com:portabilis/i-educar.git && cd i-educar
```

Copie o arquivo `docker-compose.example.yml` e faça as configurações para o seu ambiente:

```bash 
cp docker-compose.example.yml docker-compose.override.yml
``` 

Faça o build das imagens Docker utilizadas no projeto e inicie os containers da aplicação (pode levar alguns minutos):

```bash
docker-compose up -d --build
```

Execute o comando para fazer uma nova instalação:

```bash
docker-compose exec php composer new-install
```

### Personalizando a instalação

No arquivo `docker-compose.override.yml` você pode personalizar sua instalação do i-Educar, mudando as portas dos
serviços ou o mapeamento dos volumes da aplicação.

### Xdebug

A ferramenta [Xdebug](https://xdebug.org/) está incluída no projeto com o intuito de facilitar o processo de debug
durante o desenvolvimento. Para configurá-la, modifique os valores das variáveis `XDEBUG_*` no arquivo
`docker-compose.override.yml` conforme orientações da sua IDE de desenvolvimento.

### Testes automatizados

Para executar os testes automatizados, é necessário ter o i-Educar rodando com uma base limpa, apenas a estrutura
inicial e suas migrations, crie o arquivo de configuração:

```bash
cp .env.example .env.testing
```

Execute o comando:

```bash
docker-compose exec php vendor/bin/phpunit
```

## Primeiro acesso

Acesse http://localhost para fazer o seu primeiro acesso.

O usuário padrão é: `admin` / A senha padrão é: `123456789`.

Assim que realizar seu primeiro acesso **não se esqueça de alterar a senha padrão**.
