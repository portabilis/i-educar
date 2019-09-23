[![Latest Release](https://img.shields.io/github/release/portabilis/i-educar.svg?label=latest%20release)](https://github.com/portabilis/i-educar/releases)
[![Build Status](https://travis-ci.com/portabilis/i-educar.svg?branch=2.2)](https://travis-ci.com/portabilis/i-educar)

# i-Educar

_“Lançando o maior software livre educacional do Brasil!”._

**Nós somos a Comunidade i-Educar e acreditamos que podemos transformar o nosso
país por meio da educação. Junte-se a nós!**

## Conteúdo

1. [Sobre o i-Educar](#sobre-o-i-educar)
2. [Comunicação](#comunicação)
3. [Como contribuir](#como-contribuir)
4. [Instalação](#instalação)
5. [FAQ](#perguntas-frequentes-faq)

## Sobre i-Educar

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
conduta](https://github.com/portabilis/i-educar/blob/master/code-of-conduct.md).

Além disso, gostamos de meios de comunicação assíncrona, onde não há necessidade de
respostas em tempo real. Isso facilita a produtividade individual dos
colaboradores do projeto.

| Canal de comunicação | Objetivos |
|----------------------|-----------|
| [Fórum](https://forum.ieducar.org) | - Tirar dúvidas <br>- Discussões de como instalar a plataforma<br> - Discussões de como usar funcionalidades<br> - Suporte entre membros de comunidade<br> - FAQ da comunidade (sobre o produto e funcionalidades) |
| [Issues do Github](https://github.com/portabilis/i-educar/issues/new/choose) | - Sugestão de novas funcionalidades<br> - Reportar bugs<br> - Discussões técnicas |
| [Telegram](https://t.me/ieducar ) | - Comunicar novidades sobre o projeto<br> - Movimentar a comunidade<br>  - Falar tópicos que **não** demandem discussões profundas |

Qualquer outro grupo de discussão não é reconhecido oficialmente pela
comunidade i-Educar e não terá suporte da Portabilis - mantenedora do projeto.

## Como contribuir

Contribuições são **super bem vindas**! Se você tem vontade de construir o
i-Educar junto conosco, veja o nosso [guia de contribuição](./contributing.md)
onde explicamos detalhadamente como trabalhamos e de que formas você pode nos
ajudar a alcançar nossos objetivos.

## Instalação

- [Nova instalação](#nova-instalação)
- [Primeiro acesso](#primeiro-acesso)
- [Personalizando a instalação](#personalizando-a-instalação)
- [Instalação em servidor](#instalação-em-servidor)
- [Instalação do pacote de relatórios](#instalação-do-pacote-de-relatórios)
- [Upgrade](#upgrade)

### Depêndencias

Para executar o projeto é necessário a utilização de alguns softwares para
facilitar o desenvolvimento.

#### Docker

- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/downloads)

#### Servidor

- [PHP](http://php.net/) versão 7.2 ou maior
- [Postgres](https://www.postgresql.org/) versão 9.5 ou superior
- [Nginx](https://www.nginx.com/)

As seguintes extensões do PHP são necessárias:

- bcmath
- ctype
- curl
- dom
- fileinfo
- gd
- iconv
- json
- libxml
- mbstring
- openssl
- pcre
- PDO
- pgsql
- Phar
- SimpleXML
- tokenizer
- xml
- xmlreader
- xmlwriter
- zip
- zlib

### Instalação utilizando Docker

> ATENÇÃO: Essa forma de instação tem o objetivo de facilitar demonstrações e
desenvolvimento. Não é recomendado para ambientes de produção!

Para instalar o projeto execute **todos os passos** abaixo, caso você deseje
atualizar sua instalação do i-Educar, siga os passos do [upgrade](#upgrade).

Clone o repositório:

```bash
git clone https://github.com/portabilis/i-educar.git && cd i-educar
```

Faça o build das imagens Docker utilizadas no projeto (pode levar alguns
minutos) e inicie os containers da aplicação:

```bash
docker-compose up -d --build
```

Execute o comando para fazer uma nova instalação:

```bash
docker-compose exec php composer new-install
```

#### Primeiro acesso

Após finalizada a instalação, descubra em qual endereço o i-Educar está
rodando, basta executar o comando:

```bash
docker-compose port nginx 80
```

Acesse o endereço que será exibido após rodar o comando acima.

O usuário padrão é: `admin` / A senha padrão é: `123456789`

Assim que realizar seu primeiro acesso **não se esqueça de alterar a senha
padrão**.

#### Personalizando a instalação

Você pode criar um arquivo `docker-compose.override.yml` para personalizar sua
instalação do i-Educar, mudando as portas dos serviços ou o mapeamento dos
volumes extras para a aplicação.

#### Xdebug

A ferramenta [Xdebug](https://xdebug.org/) está incluída no projeto com o
intuito de facilitar o processo de debug durante o desenvolvimento. Para
configurá-la, modifique os valores das variáveis `XDEBUG_*` no arquivo
`docker-compose.override.yml` conforme orientações da sua IDE de
desenvolvimento.

#### Executando testes unitários

Para rodar os testes, é necessário ter o i-Educar rodando e com uma base limpa,
apenas a estrutura inicial e as migrations, crie o arquivo de configuração:

```bash
cp .env.example .env.testing
```

Execute o comando:

```bash
docker-compose exec php vendor/bin/phpunit
```

### Instalação em servidor

O i-Educar possui um instalador que pode te auxiliar no processo de instalação
em um servidor para produção.

Antes de tudo faça [download](https://github.com/portabilis/i-educar/releases)
da versão mais recente do i-Educar e extraia o conteúdo do release em uma pasta
no seu servidor. O instalador está disponível desde a versão `2.1.0`.

#### Configurando o servidor

O **Nginx** precisa estar devidamente configurado para rodar o i-Educar e
permitir acesso ao instalador. Você encontra um exemplo de configuração
[aqui](https://github.com/portabilis/i-educar/blob/master/docker/nginx/default.conf).

Em sistemas **Ubuntu**, por exemplo, você colocaria este arquivo na pasta
`/etc/nginx/sites-available` e criaria um symlink para ele na pasta
`/etc/nginx/sites-enabled`.

Não esqueça de adequar a configuração de acordo com a realidade do seu servidor
principalmente as seguintes diretivas:

- `root`
- `fastcgi_pass`

Depois de tudo pronto basta reiniciar o processo do nginx para que as
configurações novas entrem em vigor.

#### Executando o instalador

Agora que o Nginx está configurado você pode acessar o instalador em:

```
http://www.example.com/install.php
```

Substitua "www.example.com" pelo seu domínio ou endereço de IP. A partir daqui o
instalador deverá te dar todas as instruções necessárias para realizar a
instalação com sucesso. Todo exemplo de comando ou código que possa vir a
aparecer no processo de instalação leva em consideração o seu ambiente, ou seja,
fique à vontade para copiar e colar os comandos que eles deverão funcionar
corretamente.

Quando tudo estiver ok você poderá definir uma senha para o usuário `admin` e
iniciar o processo de instalação. Se tudo correr bem você poderá acessar o
i-Educar normalmente.

Em caso de erros no processo de instalação verifique os logs do sistema que se
encontram em `storage/logs` para determinar suas causas. Não hesite em
[entrar em contato](#comunicação) caso enfrente dificuldades!

### Instalação do pacote de relatórios

O i-Educar possui um pacote de mais de 40 relatórios.

Para instalar o pacote de relatórios visite o repositório do projeto
[https://github.com/portabilis/i-educar-reports-package](https://github.com/portabilis/i-educar-reports-package)
e siga as instruções de instalação.

#### Upgrade

- [Upgrade para 2.2 da 2.1](https://github.com/portabilis/i-educar/wiki/Upgrade-para-2.2-da-2.1).
- [Upgrade para 2.1 da 2.0](https://github.com/portabilis/i-educar/wiki/Upgrade-para-2.1-da-2.0).

## Perguntas frequentes (FAQ)

Algumas perguntas aparecem recorrentemente. Olhe primeiro por aqui: [FAQ](https://github.com/portabilis/i-educar-website/blob/master/docs/faq.md).

---

Powered by [Portabilis Tecnologia](http://www.portabilis.com.br/).
