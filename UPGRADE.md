# Guia de atualização

Este guia tem o intuido de auxiliar no processo de atualização do i-Educar para a versão
[2.10](https://github.com/portabilis/i-educar/tree/2.10) a partir da versão
[2.9](https://github.com/portabilis/i-educar/tree/2.9).

> **Importante: faça o backup do seu banco de dados antes de iniciar qualquer procedimento.**

## Requisitos mínimos

Os requisitos que foram testados para utilizar a versão [2.10](https://github.com/portabilis/i-educar/tree/2.10) são:

| Software                                                 | Versão  | Comando                    | Descrição                   |
|----------------------------------------------------------|---------|----------------------------|-----------------------------|
| [Laravel](https://laravel.com/)                          | `12`    | `php artisan --version`    | Framework                   |
| [PHP](http://php.net/)                                   | `8.4`   | `php --version`            | Linguagem de programação    |
| [Composer](https://getcomposer.org/)                     | `2.8`   | `composer --version`       | Gerenciador de dependências |
| [Nginx](https://www.nginx.com/)                          | `1.28`  | `nginx -v`                 | Servidor web                |
| [Postgres](https://www.postgresql.org/)                  | `17`    | `psql --version`           | Banco de dados              |
| [Redis](https://redis.io/)                               | `8`     | `redis-cli --version`      | Banco de dados              |
| [Git](https://git-scm.com/)                              | `2.49`  | `git --version`            | Controle de versão          |
| [Ubuntu](https://ubuntu.com/)                            | `24.04` | `lsb_release -a`           | Sistema operacional         |
| [Docker](https://www.docker.com/) `dev`                  | `28`    | `docker --version`         | Conteinerização             |
| [Docker Compose](https://docs.docker.com/compose/) `dev` | `2.36`  | `docker compose --version` | Orquestração de containers  |

`dev`: requisito para ambiente de desenvolvimento.

## Upgrade via linha de comando

Para fazer o upgrade para a versão [2.10](https://github.com/portabilis/i-educar/tree/2.10) a partir da versão
[2.9](https://github.com/portabilis/i-educar/tree/2.9) do i-Educar você precisará executar os seguintes passos:

> Para usuários Docker, executar os comandos `# (Docker)` ao invés da linha seguinte.

```bash
git fetch
git checkout 2.9

# (Docker) docker compose exec php php artisan migrate
php artisan migrate
```

> Neste momento é necessário **fazer backup do seu banco de dados** na versão atual e fazer e a migração para a nova versão versão.

Atualize o código fonte:

```bash
# Importante: faça o backup do seu banco de dados
 
# (Docker) docker compose down
git checkout 2.10.0

# (Docker) docker compose build
# (Docker) docker compose up -d

# Importante: faça a restauração do seu banco de dados 

# (Docker) docker compose exec php composer update-install
# (Docker) docker compose exec php composer plug-and-play:update 
composer update-install
composer plug-and-play:update
```

Sua instalação estará atualizada e você poderá realizar seu
[primeiro acesso](https://github.com/portabilis/i-educar#primeiro-acesso) na nova versão do i-Educar.

### Personalizar o ambiente com Docker

O arquivo `docker-compose.yml` utiliza variáveis de
ambiente para expor as portas dos containers. Você pode adicionar no seu arquivo `.env`:

| Variável                | Descrição                 |
|-------------------------|---------------------------|
| `DOCKER_NGINX_PORT`     | Porta HTTP da aplicação   |
| `DOCKER_NGINX_SSL_PORT` | Porta HTTPS da aplicação  |
| `DOCKER_POSTGRES_PORT`  | Porta do banco de dados   |
| `DOCKER_REDIS_PORT`     | Porta do serviço de cache |

Você também pode utilizar o arquivo `docker-compose.override.yml` para mais configurações.
