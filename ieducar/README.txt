// $Id$

CONTEÚDO
--------

 * Requisitos
 * Instalação
 * Documentação
 * Suporte técnico
 * Licença


REQUISITOS
----------

O i-Educar requer um servidor web, PHP 5.2, PostgreSQL 8.2 e a biblioteca PDFLib
(versão Lite ou Commercial). O servidor web Apache 2 é recomendado mas qualquer
outro com suporte a PHP pode ser utilizado.

A biblioteca PDFLib Lite tem algumas restrições em sua utilização. Consulte a
licença da biblioteca para ver se o seu uso não cairá na necessidade de adquirir
uma licença comercial:
http://www.pdflib.com/products/pdflib-family/pdflib-lite/pdflib-lite-licensing


INSTALAÇÃO
----------

1. DOWNLOAD DO SOFTWARE

   Faça o download dos arquivos do sistema antes de prosseguir. A versão atual
   pode ser encontrada em:
   http://www.softwarepublico.gov.br/dotlrn/clubs/ieducar/file-storage/index?folder_id=10855442.
   Descompacte o pacote de sua preferência no diretório raiz do seu servidor web
   Apache.

      $ cd /var/www
      $ mkdir ieducar; cd ieducar
      $ tar -xzvf /caminho/pacotes/ieducar-X.X.X.tar.gz


2. CRIE O BANCO DE DADOS

   Crie o banco de dados ao qual o i-Educar usará para armazenar todos os dados
   digitados através da interface web. Os passos descritos nessa seção irão
   criar:

      * Um usuário ieducar no servidor PostgreSQL com a senha de acesso ieducar;
      * Um banco de dados ieducar.

   Observação: você pode usar o nome de usuário, banco de dados e senha que
   desejar. Esses são apenas nomes padrões que a aplicação usa para conectar-se
   ao banco.

   Faça login no servidor de banco de dados PostgreSQL com o cliente psql:

      $ su
      # su - postgres
      # psql

   Alternativamente, com o sudo:

      $ sudo -u postgres psql

   Crie o usuário de banco de dados que será utilizado pelo i-Educar:

      postgres=# CREATE ROLE ieducar;
      postgres=# ALTER ROLE ieducar WITH SUPERUSER INHERIT NOCREATEROLE \
         CREATEDB LOGIN PASSWORD 'ieducar';

   Crie o banco de dados:

      postgres=# CREATE DATABASE ieducar WITH TEMPLATE = template0 \
         OWNER = ieducar ENCODING = 'LATIN1';
      postgres=# \q

   Execute o arquivo ieducar.sql que vem no i-Educar. O diretório em que esse 
   arquivo reside é o misc/database.

      $ psql -d ieducar -f misc/database/ieducar.sql

   Atenção: em algumas plataformas, o restore do banco pode acabar em um erro
   FATAL. Se isso acontecer, experimente fazer o restore no mesmo diretório em
   que se encontra o arquivo ieducar.sql.

   Novamente no psql, execute o seguinte comando para configurar o search_path:

      $ psql ieducar
      postgres=# ALTER DATABASE ieducar SET search_path TO "$user", public, \
        portal, cadastro, acesso, alimentos, consistenciacao, historico, \
        pmiacoes, pmicontrolesis, pmidrh, pmieducar, pmiotopic, urbano;
      postgres=# \q;


3. EDITE O ARQUIVO DE CONFIGURAÇÃO E CONCEDA PERMISSÕES DE ESCRITA

   O i-Educar armazena algumas configurações necessárias para a aplicação em um
   arquivo chamado ieducar.ini (em configuration/), que possui uma sintaxe bem
   simples de entender. Caso tenha criado o banco de dados, nome de usuário ou
   senha com um valor diferente de ieducar, basta editar esse arquivo para que
   corresponda as suas escolhas:

      [production]
      ; Configurações de banco de dados
      app.database.dbname   = ieducar
      app.database.username = ieducar
      app.database.hostname = localhost
      app.database.password = ieducar
      app.database.port     = 5432

   Exemplo: caso tenha nomeado seu banco de dados com ieducar_db, o usuário com
   ieducar_user e a senha com ieducar_pass, o ieducar.ini ficaria da seguinte
   forma:

      [production]
      ; Configurações de banco de dados
      app.database.dbname   = ieducar_db
      app.database.username = ieducar_user
      app.database.hostname = localhost
      app.database.password = ieducar_pass
      app.database.port     = 5432

   Depois, conceda permissões de escrita nos diretórios intranet/tmp e
   intranet/pdf. Uma forma prática é dar permissão de escrita para o usuário
   dono do diretório e para usuários de um grupo. Nesse caso, mudaremos o grupo
   desses diretórios para o grupo do usuário Apache.

      # chmod 775 intranet/tmp intranet/pdf
      # chgrp www-data intranet/tmp intranet/pdf

   Observação: www-data é o nome do grupo Apache padrão em sistemas Debian.
   Em outros sistemas, esse nome pode ser httpd, apache ou _www. Substitua de
   acordo com o usado em seu sistema operacional.


4. CONFIGURE O APACHE OU CRIE UM VIRTUAL HOST

   A partir da versão 1.1.X, o i-Educar inclui, por padrão, um arquivo chamado
   .htaccess no diretório raiz da aplicação. Esse arquivo contém diretivas de
   configuração do servidor Apache que tornam o i-Educar mais seguro.
   Além disso, esse arquivo configura o PHP corretamente para as necessidades
   da aplicação.

   Para que esse arquivo seja executado a cada requisição, é necessário
   configurar o Apache para que este execute os arquivos .htaccess ou criar um
   Virtual Host. A primeira opção requer a edição do arquivo
   /etc/apache2/site-available/default. A única diretiva a ser alterada é
   AllowOverride (linha 11) para All:

        9         <Directory /var/www/>
       10                 Options Indexes FollowSymLinks MultiViews
       11                 AllowOverride All
       12                 Order allow,deny
       13                 allow from all
       14         </Directory>

   Reinicie o servidor Apache:

      $ /etc/init.d/apache2 restart

   A segunda opção requer a criação de um novo arquivo em
   /etc/apache2/sites-available/. Crie um arquivo chamado ieducar.local com o
   seguinte conteúdo:

      <VirtualHost *:80>
        ServerName ieducar.local
        DocumentRoot /var/www/ieducar

        <Directory /var/www/ieducar>
          AllowOverride all
          Order deny,allow
          Allow from all
        </Directory>
      </VirtualHost>

   Edite o arquivo /etc/hosts (no Windows esse arquivo fica em
   C:\WINDOWS\system32\drivers\etc\hosts) e adicione a seguinte linha:

      127.0.0.1      ieducar.local

   Reinicie o servidor Apache:

      $ /etc/init.d/apache2 restart

   Pronto. Agora, acesse o endereço http://ieducar.local em seu navegador.

   Atenção: configurar o seu servidor Apache (seguindo uma das opções
   apresentadas) é importante para a segurança da aplicação. Assim, evita-se que
   arquivos importantes como o configuration/ieducar.ini e os relatórios gerados
   pela aplicação fiquem publicamente expostos para leitura através da Internet.


5. ACESSE A APLICAÇÃO

   Abra o navegador de sua preferência e acesse o endereço
   http://localhost/ieducar ou http://ieducar.local (caso tenha configurado um
   Virtual Host). Faça o login na aplicação utilizando o usuário administrador.
   O login e senha para acesso são admin e admin, respectivamente.


6. CONFIGURE O PHP

   Esse passo é opcional caso tenha configurado o Apache (via AllowOverride ou
   VirtualHost). Edite o arquivo php.ini da seguinte forma:

   * memory_limit: altere para, no mínimo, 32M (devido a geração de relatórios
   consumir bastante memória, pode ser necessário aumentar para uma quantidade
   maior em plataformas 64 bits);
      memory_limit = 32M

   * error_reporting: altere para E_ALL & ~E_NOTICE para evitar que avisos do
   nível E_NOTICE (comuns na versão atual), apareçam nas telas quebrando o
   layout do sistema. E_ERROR é o recomendado para ambientes de produção.
      error_reporting = E_ALL & ~E_NOTICE

   * display_errors: altere para Off em produção:
      display_errors = Off

   * short_open_tag: altere para On.
      short_open_tag = On

   Observação: a localização do arquivo php.ini é diferente entre os sistemas
   operacionais. No Debian/Ubuntu, o padrão é /etc/php5/apache2/php.ini. Para
   descobrir onde o arquivo fica em seu sistema operacional, acesse o endereço
   http://localhost/ieducar/info.php e procure por Loaded Configuration File.

   Após qualquer alteração no arquivo php.ini, reinicie seu servidor web:

      # /etc/init.d/apache2 restart


7. FONTE

   * https://svn.softwarepublico.gov.br/trac/ieducar/wiki/Documentacao/1.1.X/Instalacao


DOCUMENTAÇÃO
------------

A documentação oficial do i-Educar está disponível em wiki:
http://svn.softwarepublico.gov.br/trac/ieducar/wiki

Problemas comuns de instalação podem ser encontrados no FAQ (perguntas
frequentes):
http://svn.softwarepublico.gov.br/trac/ieducar/wiki/Documentacao/FAQ/1.X


SUPORTE TÉCNICO
---------------

Suporte técnico pode ser encontrado nos fóruns da comunidade i-Educar no Portal
do Software Público Brasileiro (requer cadastro):
http://www.softwarepublico.gov.br/dotlrn/clubs/ieducar


LICENÇA
-------

O i-Educar é um Software Público Brasileiro (SPB), livre e licenciado pela
Creative Commons Licença Pública Geral versão 2 traduzida (CC GNU/GPL 2). Uma
cópia da licença está incluida nesta distribuição no arquivo LICENSE-pt_BR.txt.