# i-Educar

_“Lançando o maior software livre educacional do Brasil!”._

**Nós somos a Comunidade i-Educar e acreditamos que podemos transformar o nosso
país por meio da educação. Junte-se a nós!**

O i-Educar é um software livre e público totalmente on-line que torna mais fácil
e prática a gestão dos processos das escolas, matrículas e dados de alunos,
apoiando os profissionais da rede de ensino e gestores a resolverem os desafios
da educação dentro e fora da sala de aula.

## Conteúdo

1. [Cenário atual](#cenário-atual)
2. [Sobre o i-Educar](#sobre-o-i-educar)
3. [Instalação](#instalação)
4. [Solicite demonstração do software](#solicite-demonstração-do-software)
5. [Solicite prestação de serviço](#solicite-prestação-de-serviço)

## Cenário atual

No final de 2017, a Portabilis, organização que é integrante da comunidade desde
2009 e tem o papel de mantenedora do projeto, propôs uma renovação de energias
para levar o i-Educar ainda mais longe. (Leia aqui o manifesto:
[i-Educar por todo o Brasil](https://softwarepublico.gov.br/social/i-educar/blog/em-2018-queremos-o-i-educar-por-todo-o-brasil)).

No sentido desta iniciativa, está a proposta de acabar com a defasagem da versão
aberta e aumentar a aproximação com os seus usuários. No dia 31 de janeiro, a
Portabilis anunciou o apoio da Fundaço Lemann para potencializar todos esses
objetivos (Saiba mais aqui:
[Preparação do lançamento do maior software livre educacional do Brasil!](https://medium.com/portabilis/prepara%C3%A7%C3%A3o-do-lan%C3%A7amento-do-maior-software-educacional-open-source-do-brasil-305e57143372)).

### Etapas iniciais deste processo

1. Liberar na versão Comunidade as principais melhorias feitas pela Portabilis
   nos últimos anos;
2. A Portabilis irá mudar seu fluxo de trabalho passando a desenvolver
   diretamente no repositório da Comunidade;
3. Planejar tecnicamente o projeto, com foco em refatoração e criação de
   padrões, para possibilitar o crescimento e expansão do uso do i-Educar;
4. Iniciar as refatorações propostas na fase de planejamento permitindo
   contribuições de forma mais ativa;
5. Evoluir constantemente o i-Educar com ênfase nos usuários e na facilidade do
   uso em redes públicas de ensino.

## Sobre o i-Educar

O i-Educar é um software livre e público para a gestão escolar que foi
desenvolvido originalmente pela Prefeitura de Itajaí-SC e disponibilizado como
Software Livre no Portal do Software Público Brasileiro
(https://softwarepublico.gov.br), onde atualmente é mantido pela Comunidade
i-Educar.

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
$ git clone git@github.com:portabilis/i-educar.git i-educar
$ cd i-educar
$ docker-compose up -d
```

Depois disto faça uma cópia do arquivo `ieducar/configuration/ieducar.ini.sample`
para `ieducar/configuration/ieducar.ini` realizando as alterações necessárias.

### Instalando relatórios

Os relatórios respondem por uma parte muito importante o i-Educar mas o
desenvolvimento destes relatórios ocorre de forma paralela em outro repositório.
Por isso, antes de prosseguir, é necessário "instalar" os relatórios em conjunto
com o i-Educar. Execute o seguinte comando a partir da pasta onde o i-Educar foi
clonado em sua máquina:

```terminal
$ git clone git@github.com:portabilis/i-educar-reports-package.git ieducar/modules/Reports
```

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
nosso banco. O Phinx já é instalado como dependência através do composer no
passo anterior, mas é necessário configurá-lo antes de executar qualquer
comando.

Na raiz do projeto você encontra um arquivo chamado `phinx.php.sample`. Copie
este arquivo e altere seu nome para `phinx.php`. Verifique seu conteúdo e,
caso tenha feito alguma mudança na configuração do docker, modifique as
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

## Solicite demonstração do software

Para solicitar uma demonstração, nos envie uma mensagem ou nos ligue por meio da
nossa [página de contato](http://goo.gl/O0JBs).

## Solicite prestação de serviço

Para solicitar serviço de migração de dados, instalação, atualização, correções
ou desenvolvimento de alterações, entre em contato por meio da nossa
[página de contato](http://goo.gl/O0JBs).

---

[Portabilis Tecnologia](http://www.portabilis.com.br/)
