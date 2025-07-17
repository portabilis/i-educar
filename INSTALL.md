# Guia de instalação

Você pode instalar o i-Educar utilizando Docker ou diretamente no seu servidor web, caso você deseje atualizar sua 
instalação siga os passos do [guia de atualização](UPGRADE.md).

- [Dependências](#dependências)
- [Instalação utilizando Docker](#instalação-utilizando-docker)
- [Instalação em servidor web](#instalação-em-servidor-web)
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

## Instalação utilizando Docker

Para instalar o projeto execute **todos os passos** abaixo:

> ATENÇÃO: Essa forma de instação tem o objetivo de facilitar demonstrações e
desenvolvimento. Não é recomendado para ambientes de produção!

Clone o repositório:

```bash
git clone git@github.com:portabilis/i-educar.git && cd i-educar
```

Configure as variáveis de ambiente que desejar:

```bash
cp .env.example .env
```

**Evitar problemas de permissão dos arquivos**

Ao utilizar Docker, os arquivos criados dentro do container (como `vendor/`, `storage/logs`, etc.) podem acabar sendo 
atribuídos ao usuário `root`, causando erros de permissão durante o desenvolvimento no host (ex: `Permission denied` ao
escrever logs).

Para evitar esse problema, o ambiente Docker do i-Educar permite configurar o **UID e GID do usuário do host** (por 
exemplo, `ieducar`) no momento do build da imagem.

Esses valores devem ser definidos no seu arquivo `.env`, da seguinte forma:

```env
HOST_UID=1001  # Use `id -u` para descobrir o UID do seu usuário local
HOST_GID=1001  # Use `id -g` para descobrir o GID do seu grupo local
```

Esses valores são utilizados durante o build do container para criar um usuário interno com o mesmo UID e GID, garantindo que os arquivos gerados dentro do container sejam acessíveis normalmente no seu host.

> Importante: caso você não defina essas variáveis, valores padrão como 1001 serão utilizados. Isso evita que os arquivos sejam criados como root (UID 0), mas ainda assim pode gerar erros de permissão se o UID/GID do container não corresponder ao do seu usuário local.

Faça o build das imagens Docker utilizadas no projeto e inicie os containers da aplicação (pode levar alguns minutos):

```bash
docker compose up -d --build
```

Execute o comando para fazer uma nova instalação:

```bash
docker compose exec php composer new-install
docker compose exec php php artisan db:seed
```

### Personalizando a instalação

Você pode criar o arquivo `docker-compose.override.yml` para personalizar sua instalação do i-Educar, mudando as portas
dos serviços ou o mapeamento dos volumes.

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
docker compose exec php vendor/bin/pest
```

## Instalação em servidor web

Para instalar o projeto execute **todos os passos** abaixo conectado em seu servidor web:

> Este passo a passo serve para um servidor Ubuntu 24.04 LTS e não tem configurações mínimas de segurança

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
apt install -y nginx redis postgresql postgresql-contrib openjdk-8-jdk openssl unzip php8.4-common php8.4-cli php8.4-fpm php8.4-bcmath php8.4-curl php8.4-mbstring php8.4-pgsql php8.4-xml php8.4-zip php8.4-gd
```

Inicie o serviço de banco de dados:

```bash
systemctl start postgresql.service
sudo -i -u postgres
```

Crie o usuário do banco de dados, quando solicitado uma senha, utilize `ieducar`:

```bash
createuser ieducar --superuser --createdb --pwprompt
```

Crie o banco de dados e volte ao usuário `root`:

```bash 
createdb ieducar
exit
```

Configure o Composer:

```bash 
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
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
cp /var/www/ieducar/docker/nginx/conf.d/* /etc/nginx/conf.d/
cp /var/www/ieducar/docker/nginx/snippets/* /etc/nginx/snippets/
sed -i 's/fpm:9000/unix:\/run\/php\/php-fpm.sock/g' /etc/nginx/conf.d/upstream.conf
rm /etc/nginx/sites-enabled/default
nginx -s reload
```

Faça a instalação do i-Educar:

```bash 
composer new-install
```

Popule o banco de dados com os dados iniciais necessários para o funcionamento:

```bash 
php artisan db:seed
```

Este passo é opcional, mas caso você desejar, você pode popular o banco de dados com alguns dados iniciais utilizando o 
comando abaixo:

```bash 
php artisan db:seed --class=DemoSeeder
```

## Primeiro acesso

Acesse http://localhost ou o IP do seu servidor para fazer o seu primeiro acesso.

O usuário padrão é: `admin` / A senha padrão é: `123456789`.

Assim que realizar seu primeiro acesso **não se esqueça de alterar a senha padrão**.
