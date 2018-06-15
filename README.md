# i-Educar

_“Lançando o maior software livre educacional do Brasil!”._

**Nós somos a Comunidade i-Educar e acreditamos que podemos transformar o nosso
país por meio da educação. Junte-se a nós!**

## Conteúdo

1. [Sobre o i-Educar](#sobre-o-i-educar)
2. [Comunicação](#comunicacao)
3. [Roadmap de tecnologia](#roadmap-de-tecnologia)
4. [Como contribuir](#como-contribuir)
5. [Instalação](#instalação)

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

### Issues do Github

Acreditamos no meio de comunicação assíncrona, onde não há necessidade de
respostas em tempo real. Isso facilita a produtividade individual dos
colaboradores do projeto.

Em breve iremos liberar um fórum, mas até lá, vamos usar as ferramentas que
temos disponíveis.

Usamos a ferramenta de issues do Github para:
- tirar dúvidas;
- reportar bugs;
- pedir recursos;
- discussões gerais.

Para mais informações de como fazer [leia
aqui](https://github.com/portabilis/i-educar/blob/master/CONTRIBUTING.md).

### Telegram

Mantemos um grupo oficial no Telegram desde dezembro de 2016 e que pode ser usado para:
  - comunicar novidades sobre o projeto;
  - movimentar a comunidade;
  - compartilhar conhecimento;
  - falar tópicos que **não** demandem discussões profundas (para essas, usar o
  Github).

Vamos fazer parte? [Acesse aqui](https://t.me/ieducar)!


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

Antes de começar você vai precisar instalar o Docker e o Docker Compose em sua
máquina. Para mais informações veja estes links:

- [Docker](https://docs.docker.com/install/) (> 18.03.1-ce)
- [Docker Compose](https://docs.docker.com/compose/install/) (> 1.21.2)

Você também vai precisar do [Git](https://git-scm.com/downloads) caso ainda não
o tenha instalado.

Depois de ter o Docker e git instalados faça o clone deste repositório e execute
o Docker Compose para criar os containers da aplicação:

```terminal
$ git clone https://github.com/portabilis/i-educar.git i-educar
$ cd i-educar
$ cp .env.example .env
$ docker-compose up -d
```

Depois disto faça uma cópia do arquivo `ieducar/configuration/ieducar.ini.sample`
para `ieducar/configuration/ieducar.ini` realizando as alterações necessárias.

### Instalando relatórios

Os relatórios respondem por uma parte muito importante no i-Educar mas o
desenvolvimento destes relatórios ocorre de forma paralela em outro repositório.
Por isso, antes de prosseguir, é necessário "instalar" os relatórios em conjunto
com o i-Educar. Execute o seguinte comando a partir da pasta onde o i-Educar foi
clonado em sua máquina:

```terminal
$ git clone https://github.com/portabilis/i-educar-reports-package.git ieducar/modules/Reports
```

P.S.: Esses relatórios são legados e podem não funcionar. Em breve vamos lançar
um pacote de mais de 40 relatórios funcionais.

### Instalando outras dependências

O i-Educar usa o [Composer](https://getcomposer.org/) para gerenciar suas
dependências. O Composer já vem pré-instalado na imagem via Docker então para
instalar as dependências use os seguintes comandos:

```terminal
$ docker-compose exec ieducar_1604 composer install
```

### Inicializando o banco de dados

O próximo passo é inicializar o banco de dados do i-Educar. Nós utilizamos o
[Phinx](https://phinx.org/) para executar migrações e preencher os dados em
nosso banco. O Phinx já é instalado como dependência através do Composer no
passo anterior, mas é necessário configurá-lo antes de executar qualquer
comando.

Na raiz do projeto você encontra um arquivo chamado `phinx.php.sample`. Copie
este arquivo e altere seu nome para `phinx.php`. Verifique seu conteúdo e,
caso tenha feito alguma mudança na configuração do Docker, modifique as
credenciais do banco de acordo com suas alterações. Caso contrário o arquivo
estará pronto para ser utilizado.

**Atenção:**

Se quiser rodar o Phinx a partir de sua própria máquina, fora de um container,
modifique a chave `host` para `localhost` e `port` para `5434`.

Depois de ter feito a configuração do Phinx, basta rodar os seguintes comandos:

```terminal
$ docker-compose exec ieducar_1604 ieducar/vendor/bin/phinx seed:run -s StartingSeed -s StartingForeignKeysSeed
$ docker-compose exec ieducar_1604 ieducar/vendor/bin/phinx migrate
```

Este comando irá executar a criação de tabelas e inserção de dados iniciais
para utilização do i-Educar.

### Configurando permissões

Para que tudo funcione adequadamente, principalmente a parte de relatórios, é
necessário definir algumas permissões especiais em pastas e arquivos. Use os
comandos abaixo:

```terminal
$ docker-compose exec ieducar_1604 chmod +x ieducar/vendor/portabilis/jasperphp/src/JasperStarter/bin/jasperstarter
$ docker-compose exec ieducar_1604 chmod 777 -R ieducar/modules/Reports/ReportSources/Portabilis
```

### Primeiro acesso

Após realizar a instalação de acordo com as instruções acima você está pronta a
realizar seu primeiro acesso ao i-Educar. Basta acessar o seguinte endereço:

[http://localhost:8001](http://localhost:8001)

O usuário padrão é: `admin` / A senha padrão é: `123456789`

Assim que realizar seu primeiro acesso **não se esqueça de alterar a senha padrão**.

### Utilização do Xdebug

A ferramenta [Xdebug](https://xdebug.org/) está incluída no projeto com o 
intuito de facilitar o processo de debug durante o desenvolvimento. Para 
configurá-la, modifique os valores das variáveis `XDEBUG_*` no arquivo `.env` 
conforme orientações da sua IDE de desenvolvimento.

---

Powered by [Portabilis Tecnologia](http://www.portabilis.com.br/)
