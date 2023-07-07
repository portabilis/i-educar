# Upgrade para 2.8 da 2.7

Este guia tem o intuido de auxiliar o processo de atualização do i-Educar da versão 
[2.8](https://github.com/portabilis/i-educar/tree/2.8) a partir da versão 
[2.7](https://github.com/portabilis/i-educar/tree/2.7).

> **Importante: faça o backup do seu banco de dados antes de iniciar qualquer procedimento.**

## Requisitos mínimos

Os requisitos que foram testados para utilizar a versão [2.8](https://github.com/portabilis/i-educar/tree/2.8) são:

| Software             | Versão | Comando                    | Descrição                   |
|----------------------|--------|----------------------------|-----------------------------|
| Laravel              | `10`   | `php artisan --version`    | Framework                   |
| PHP                  | `8.2`  | `php --version`            | Linguagem de programação    |
| Composer             | `2.5`  | `composer --version`       | Gerenciador de dependências |
| Nginx                | `1.25` | `nginx -v`                 | Servidor web                |
| Postgres             | `15`   | `psql --version`           | Banco de dados              |
| Redis                | `7`    | `redis-cli --version`      | Banco de dados              |
| Git                  | `2.40` | `git --version`            | Controle de versão          |
| Docker `dev`         | `24`   | `docker --version`         | Containerização             |
| Docker Compose `dev` | `2.19` | `docker-compose --version` | Orquestração de containers  |

## Upgrade via linha de comando

Para fazer o upgrade para a versão [2.8](https://github.com/portabilis/i-educar/tree/2.8) a partir da versão
[2.7](https://github.com/portabilis/i-educar/tree/2.7) do i-Educar você precisará executar os seguintes passos:

> Para usuários Docker, executar os comandos `# (Docker)` ao invés da linha seguinte.

```bash
git fetch
git checkout 2.7

# (Docker) docker-compose exec php artisan migrate
php artisan migrate
```

Neste momento é necessário **fazer backup do seu banco de dados** Postgres versão 14, instalar a versão 15 e **fazer a 
restauração do banco de dados** na nova versão.

Atualize o código fonte:

```bash
# Importante: faça o backup do seu banco de dados
 
# (Docker) docker-compose down

git checkout 2.8.0

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
