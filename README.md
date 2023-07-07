[![Latest Release](https://img.shields.io/github/release/portabilis/i-educar.svg?label=latest%20release)](https://github.com/portabilis/i-educar/releases)
[![Build Status](https://github.com/portabilis/i-educar/actions/workflows/tests.yml/badge.svg)](https://github.com/portabilis/i-educar/actions)

# i-Educar

_“Lançando o maior software livre educacional do Brasil!”._

**Nós somos a Comunidade i-Educar e acreditamos que podemos transformar o nosso
país por meio da educação. Junte-se a nós!**

- [Sobre o i-Educar](#sobre-o-i-educar)
- [Comunicação](#comunicação)
- [Como contribuir](#como-contribuir)
- [Instalação](#instalação)
- [FAQ](#perguntas-frequentes-faq)

## Sobre o i-Educar

O i-Educar é um software livre de gestão escolar totalmente on-line que permite
secretários escolares, professores, coordenadores e gestores da área possam
utilizar uma ferramenta que produz informações e estatísticas em tempo real,
com um banco de dados centralizado e de fácil acesso, diminuindo a necessidade
de uso de papel, a duplicidade de documentos, o tempo de atendimento ao cidadão
e racionalizando o trabalho do servidor público.

Ele foi originalmente desenvolvido pela prefeitura de Itajaí - SC e
disponibilizado no Portal do Software Público do Governo Federal em 2008, com o
objetivo de atender às necessidades das Secretarias de Educação e Escolas
Públicas de **todo o Brasil**.

## Comunicação

Acreditamos que o sucesso do projeto depende diretamente da interação clara e
objetiva entre os membros da Comunidade. Por isso, estamos definindo algumas
políticas para que estas interações nos ajudem a crescer juntos! Você pode
consultar algumas destas boas práticas em nosso [código de
conduta](https://github.com/portabilis/i-educar/blob/master/CODE-OF-CONDUCT.md).

Além disso, gostamos de meios de comunicação assíncrona, onde não há necessidade de
respostas em tempo real. Isso facilita a produtividade individual dos
colaboradores do projeto.

| Canal de comunicação                                                         | Objetivos                                                                                                                                                                                                          |
|------------------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [Fórum](https://forum.ieducar.org)                                           | - Tirar dúvidas <br>- Discussões de como instalar a plataforma<br> - Discussões de como usar funcionalidades<br> - Suporte entre membros de comunidade<br> - FAQ da comunidade (sobre o produto e funcionalidades) |
| [Issues do Github](https://github.com/portabilis/i-educar/issues/new/choose) | - Sugestão de novas funcionalidades<br> - Reportar bugs<br> - Discussões técnicas                                                                                                                                  |
| [Telegram](https://t.me/ieducar )                                            | - Comunicar novidades sobre o projeto<br> - Movimentar a comunidade<br>  - Falar tópicos que **não** demandem discussões profundas                                                                                 |

Qualquer outro grupo de discussão não é reconhecido oficialmente pela
comunidade i-Educar e não terá suporte da Portabilis - mantenedora do projeto.

## Como contribuir

Contribuições são **super bem-vindas**! Se você tem vontade de construir o
i-Educar junto conosco, veja o nosso [guia de contribuição](./CONTRIBUTING.md)
onde explicamos detalhadamente como trabalhamos e de que formas você pode nos
ajudar a alcançar nossos objetivos.

## Instalação

- [Dependências](#dependências)
- [Instalação utilizando Docker](#instalação-utilizando-docker)
- [Primeiro acesso](#primeiro-acesso)
- [Pacotes (módulos)](#pacotes-módulos)
- [Upgrade](#upgrade)

### Dependências

Para executar o projeto é necessário a utilização de alguns softwares.

#### Servidor

- [PHP](http://php.net/)
- [Composer](https://getcomposer.org/)
- [Nginx](https://www.nginx.com/)
- [Postgres](https://www.postgresql.org/)
- [Redis](https://redis.io/)
- [Git](https://git-scm.com/downloads)

#### Docker

- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Instalação utilizando Docker

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

#### Personalizando a instalação

No arquivo `docker-compose.override.yml` você pode personalizar sua instalação do i-Educar, mudando as portas dos 
serviços ou o mapeamento dos volumes da aplicação.

#### Xdebug

A ferramenta [Xdebug](https://xdebug.org/) está incluída no projeto com o intuito de facilitar o processo de debug 
durante o desenvolvimento. Para configurá-la, modifique os valores das variáveis `XDEBUG_*` no arquivo
`docker-compose.override.yml` conforme orientações da sua IDE de desenvolvimento.

#### Testes automatizados

Para executar os testes automatizados, é necessário ter o i-Educar rodando com uma base limpa, apenas a estrutura
inicial e suas migrations, crie o arquivo de configuração:

```bash
cp .env.example .env.testing
```

Execute o comando:

```bash
docker-compose exec php vendor/bin/phpunit
```

### Primeiro acesso

Acesse http://localhost para fazer o seu primeiro acesso.

O usuário padrão é: `admin` / A senha padrão é: `123456789`.

Assim que realizar seu primeiro acesso **não se esqueça de alterar a senha padrão**.

### Pacotes (módulos)

O i-Educar possui um conjunto de pacotes (módulos) que o extendem e o tornam um software mais robusto. Para instalar um 
pacote, siga as instruções de instalação encontradas no repositório do projeto.

- [Módulo de Relatórios](https://github.com/portabilis/i-educar-reports-package/)
- [Módulo de Biblioteca](https://github.com/portabilis/i-educar-library-package/)
- [Módulo de Transporte](https://github.com/portabilis/i-educar-transport-package/)

### Upgrade

Para realizar o _upgrade_ da versão do i-Educar, considere seguir os passos do [guia de atualização](UPGRADE.md).

## Perguntas frequentes (FAQ)

Algumas perguntas aparecem recorrentemente. Olhe primeiro por aqui: [FAQ](https://github.com/portabilis/i-educar-website/blob/master/docs/faq.md).

---

Powered by [Portábilis](https://portabilis.com.br/).
