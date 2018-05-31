# i-Educar

i-Educar é um software público para gestão escolar, desenvolvido originalmente
pela Prefeitura de Itajai-SC, o qual foi disponibilizado e apoiado pelo Governo
Federal por meio do
[Portal do Software Público Brasileiro](http://www.softwarepublico.gov.br/).

## Instalação

Antes de começar você vai precisar instalar o Docker e o Docker Compose em sua
máquina. Para mais informações veja estes links:

- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

Depois de ter o Docker instalado faça o clone deste repositório e execute o
Docker Compose para criar os containers da aplicação:

```terminal
$ git clone git@github.com:portabilis/i-educar.git i-educar
$ cd i-educar
$ docker-compose up -d
```

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
$ docker-compose exec ieducar_1604 bash
$ cd ieducar
$ composer install
$ exit
```

### Inicializando o banco de dados

O próximo passo é inicializar o banco de dados do i-Educar. Para isto basta
executar os seguintes comandos:

```terminal
$ ieducar/vendor/bin/phinx seed:run -s StartingSeed -s StartingForeignKeysSeed
$ ieducar/vendor/bin/phinx migrate
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

## Primeiro acesso

Após realizar a instalação de acordo com as instruções acima você está pronta a
realizar seu primeiro acesso ao i-Educar. Basta acessar o seguinte endereço:

[http://localhost:8001](http://localhost:8001)

O usuário padrão é: `admin`
A senha padrão é: `123456789`

Assim que realizar seu primeiro acesso
**não se esqueça de alterar a senha padrão**.

## Solicite demonstração do software

Para solicitar uma demonstração, nos envie uma mensagem ou nos ligue por meio da
nossa [página de contato](http://goo.gl/O0JBs).

## Solicite prestação de serviço

Para solicitar serviço de migração de dados, instalação, atualização, correções
ou desenvolvimento de alterações, entre em contato por meio da nossa
[página de contato](http://goo.gl/O0JBs).

---

[Portabilis Tecnologia](http://www.portabilis.com.br/)
