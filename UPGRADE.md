# Guia de atualização

Este guia tem o intuido de auxiliar no processo de atualização do i-Educar para a versão 
[2.9](https://github.com/portabilis/i-educar/tree/2.9) a partir da versão 
[2.8](https://github.com/portabilis/i-educar/tree/2.8).

> **Importante: faça o backup do seu banco de dados antes de iniciar qualquer procedimento.**

## Requisitos mínimos

Os requisitos que foram testados para utilizar a versão [2.9](https://github.com/portabilis/i-educar/tree/2.9) são:

| Software                                                 | Versão  | Comando                    | Descrição                   |
|----------------------------------------------------------|---------|----------------------------|-----------------------------|
| [Laravel](https://laravel.com/)                          | `11`    | `php artisan --version`    | Framework                   |
| [PHP](http://php.net/)                                   | `8.3`   | `php --version`            | Linguagem de programação    |
| [Composer](https://getcomposer.org/)                     | `2.7`   | `composer --version`       | Gerenciador de dependências |
| [Nginx](https://www.nginx.com/)                          | `1.26`  | `nginx -v`                 | Servidor web                |
| [Postgres](https://www.postgresql.org/)                  | `16`    | `psql --version`           | Banco de dados              |
| [Redis](https://redis.io/)                               | `7`     | `redis-cli --version`      | Banco de dados              |
| [Git](https://git-scm.com/)                              | `2.45`  | `git --version`            | Controle de versão          |
| [Ubuntu](https://ubuntu.com/)                            | `22.04` | `lsb_release -a`           | Sistema operacional         |
| [Docker](https://www.docker.com/) `dev`                  | `26`    | `docker --version`         | Containerização             |
| [Docker Compose](https://docs.docker.com/compose/) `dev` | `2.27`  | `docker-compose --version` | Orquestração de containers  |

`dev`: requisito para ambiente de desenvolvimento.

## Upgrade via linha de comando

Para fazer o upgrade para a versão [2.9](https://github.com/portabilis/i-educar/tree/2.9) a partir da versão
[2.8](https://github.com/portabilis/i-educar/tree/2.8) do i-Educar você precisará executar os seguintes passos:

> Para usuários Docker, executar os comandos `# (Docker)` ao invés da linha seguinte.

```bash
git fetch
git checkout 2.8

# (Docker) docker-compose exec php artisan migrate
php artisan migrate
```

Neste momento é necessário **fazer backup do seu banco de dados** Postgres versão 15, instalar a versão 16 e **fazer a 
restauração do banco de dados** na nova versão.

Atualize o código fonte:

```bash
# Importante: faça o backup do seu banco de dados
 
# (Docker) docker-compose down

git checkout 2.9.0

# (Docker) docker-compose build
# (Docker) docker-compose up -d

# Importante: faça a restauração do seu banco de dados 

# (Docker) docker-compose exec php composer update-install
# (Docker) docker-compose exec php composer plug-and-play:update 
composer update-install
composer plug-and-play:update
```

Sua instalação estará atualizada e você poderá realizar seu
[primeiro acesso](https://github.com/portabilis/i-educar#primeiro-acesso) na nova versão do i-Educar.

### Configurar ambiente com Docker

Na versão [2.9](https://github.com/portabilis/i-educar/tree/2.9) o arquivo `docker-compose.yml` não expõem nenhuma porta
dos containers para evitar conflitos. Para isso, utilize o arquivo `docker-compose.override.yml` e configure quais 
portas deseja que os serviços sejam executados.

```bash 
cp docker-compose.example.yml docker-compose.override.yml
``` 

#### Arquivo de exemplo de configuração do `docker-compose.override.yml`

O exemplo abaixo possui configurações básicas que podem ser customizadas e que não irão interferir no código fonte 
original do projeto permitindo assim um maior controle do seu ambiente.

```yml 
services:

  php:
    container_name: ieducar-php
    volumes:
      - ~/.ssh:/root/.ssh:ro # permite conexões usando suas chaves SSH

  nginx:
    container_name: ieducar-nginx
    ports:
      - "80:80"   # permite acessar o i-Educar em http://localhost, ou
      - "8000:80" # permite acessar o i-Educar em http://localhost:8080

  postgres:
    container_name: ieducar-postgres
    ports:
      - "5432:5432" # permite conectar software gerenciador de banco de dados

  redis:
    container_name: ieducar-redis
    ports:
      - "6379:6379" # permite conectar software gerenciador de banco de dados
```
