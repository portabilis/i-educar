[![Latest Release](https://img.shields.io/github/release/portabilis/i-educar.svg?label=latest%20release)](https://github.com/portabilis/i-educar/releases) ![](https://scrutinizer-ci.com/g/portabilis/i-educar/badges/quality-score.png?b=master) ![](https://scrutinizer-ci.com/g/portabilis/i-educar/badges/coverage.png?b=master) ![](https://scrutinizer-ci.com/g/portabilis/i-educar/badges/build.png?b=master) ![](https://scrutinizer-ci.com/g/portabilis/i-educar/badges/code-intelligence.svg?b=master)

# i-Educar

_“Lançando o maior software livre educacional do Brasil!”._

**Nós somos a Comunidade i-Educar e acreditamos que podemos transformar o nosso
país por meio da educação. Junte-se a nós!**

## Conteúdo

1. [Sobre o i-Educar](#sobre-o-i-educar)
2. [Comunicação](#comunicação)
3. [Roadmap de tecnologia](#roadmap-de-tecnologia)
4. [Como contribuir](#como-contribuir)
5. [Instalação](#instalação)
6. [FAQ](#perguntas-frequentes-(FAQ))

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
conduta](https://github.com/portabilis/i-educar/blob/master/CODE_OF_CONDUCT.md).

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

## Roadmap de tecnologia

O i-Educar por ser um sistema antigo e por não ter seguido um padrão específico,
precisa passar por um processo de melhoria para diminuir a quantidade de bugs,
melhorar a manutenibilidade e enfim permitir a evolução através de features.

### Passos iniciais

- Adoção do [PSR1](https://www.php-fig.org/psr/psr-1/)
- Adoção do [PSR2](https://www.php-fig.org/psr/psr-2/)
- Adoção do [PSR4](https://www.php-fig.org/psr/psr-4/)
- Iniciar a cobertura de testes para possibilitar refatorações

### Planejamento Técnico

Em nossa wiki você encontra um planejamento mais técnico de como devemos
prosseguir com as melhorias e evoluções do nosso projeto.
[Clique aqui](https://github.com/portabilis/i-educar/wiki/Planejamento-T%C3%A9cnico)
para ler mais a respeito.

## Como contribuir

Contribuições são **super bem vindas**! Se você tem vontade de construir o
i-Educar junto conosco, veja o nosso [guia de contribuição](./CONTRIBUTING.md)
onde explicamos detalhadamente como trabalhamos e de que formas você pode nos
ajudar a alcançar nossos objetivos.

## Instalação

> ATENÇÃO: Essa forma de instação tem o objetivo de facilitar demonstrações e desenvolvimento. Não é recomendado para ambientes de produção!


### Depêndencias

Para executar o projeto é necessário a utilização de alguns softwares para facilitar o desenvolvimento.

- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/downloads)

### Instalação

Para instalar o projeto clone o repositório:

```
git clone https://github.com/portabilis/i-educar.git && cd i-educar
```

Em seguida, clone o repositório de relatórios:

```
git clone https://github.com/portabilis/i-educar-reports-package.git ieducar/modules/Reports
```

Será necessário criar os arquivos de configuração do seu ambiente, modifique-os se necessário:

```
cp docker-compose.yml.example docker-compose.yml
cp .env.example .env
cp ieducar/configuration/ieducar.ini.example ieducar/configuration/ieducar.ini
cp phinx.php.example phinx.php
```

Faça o build das imagens Docker utilizadas no projeto (pode levar alguns minutos):

```
docker-compose build
```

Então, inicie os containers da aplicação:

```
docker-compose up -d
```

Acesse o container `php` para permitir a escrita nas pastas necessárias, criar 
a chave da aplicação e finalizar a instalação do banco de dados e dos 
relatórios:

```
docker-compose exec php bash

chmod -R 777 bootstrap/cache
chmod -R 777 storage
chmod -R 777 ieducar/modules/Reports/ReportSources/Portabilis
chmod +x ieducar/vendor/portabilis/jasperphp/src/JasperStarter/bin/jasperstarter

php artisan key:generate

vendor/bin/phinx seed:run -s StartingSeed -s StartingForeignKeysSeed
vendor/bin/phinx migrate

exit
```

### Instalando outras dependências

O i-Educar usa o [Composer](https://getcomposer.org/) para gerenciar suas
dependências. O Composer já é executado automaticamente para quem utilizar
docker-compose, basta executar o comando `docker-compose up`.

Caso queira adicionar novas dependências ao projeto ou executar algum outro
comando do composer, execute na raiz do projeto:

```bash
docker run -it -v $(pwd):/app composer <seu_comando>
```

### Primeiro acesso

Após realizar a instalação de acordo com as instruções acima você está pronto 
para fazer o seu primeiro acesso ao i-Educar, acesse o endereço:

[http://localhost](http://localhost)

O usuário padrão é: `admin` / A senha padrão é: `123456789`

Assim que realizar seu primeiro acesso **não se esqueça de alterar a senha padrão**.

### Utilização do Xdebug

A ferramenta [Xdebug](https://xdebug.org/) está incluída no projeto com o 
intuito de facilitar o processo de debug durante o desenvolvimento. Para 
configurá-la, modifique os valores das variáveis `XDEBUG_*` no arquivo 
`docker-compose.yml` conforme orientações da sua IDE de desenvolvimento.

## Perguntas frequentes (FAQ)

Algumas perguntas aparecem recorrentemente. Olhe primeiro por aqui: [FAQ](docs/faq.md)

---

Powered by [Portabilis Tecnologia](http://www.portabilis.com.br/)
