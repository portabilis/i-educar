
Antes de começar
Todos os passos para instalação abaixo devem ser executados logado com um usuário diferente do root, 
caso você execute os comandos logado como root o banco de dados não poderá ser iniciado.
Caso você ainda não tenha criado um usuário, crie um executando: sudo useradd --create-home --groups sudo --shell /bin/bash ieducar

E então defina a senha do usuário (não esqueça esta senha, ela será usada para logar como este usuário):
sudo passwd ieducar

Após isto conecte-se como o novo usuário:
sudo su ieducar

Primeiros passos
Com um usuário diferente do root, execute os comandos abaixo:
cd ~
sudo apt-get install curl 

Instalação e configuração do ambiente de desenvolvimento
Instalação apache, php e pgvm

sudo wget https://gist.github.com/lucasdavila/4711321/raw/1_ambiente_desenvolvimento.sh

Use o vim ou o nano:
sudo vim 1_ambiente_desenvolvimento.sh 
ou 
sudo nano 1_ambiente_desenvolvimento.sh
Apague as linhas:
echo -e "\n\n** Instalando pgvm"
curl -s -L https://raw.github.com/lucasdavila/pgvm/master/bin/pgvm-self-install | bash -s -- --update
Salve o arquivo e Saia.

execute o comando  para ambiente de desenvolvimento
bash 1_ambiente_desenvolvimento.sh

Logo após execute o seguinte comando para instalar o pgvm:
curl -s -L https://raw.github.com/guedes/pgvm/master/bin/pgvm-self-install
Digite o comando:
source ~/.bashrc 

Instalação banco de dados postgresql 8.2 via pgvm
curl -L https://gist.github.com/lucasdavila/4711321/raw/2_db.sh | bash 
Se aparecer a seguinte tela:

Clone do código fonte
git clone https://github.com/portabilis/ieducar.git
Instalação dos pacotes pear (dependências i-Educar)
bash ~/ieducar/ieducar/scripts/install_pear_packages.sh 


Configuração do apache
curl -L https://gist.github.com/lucasdavila/4711321/raw/be0ca20be5e092dbe05a23cbb5ff6ba01c4b9af9/3_vhost.sh | bash 

Configuração git (optativo)
git config --global user.name "Seu Nome Completo"
git config --global user.email seu_email@dominio.com 

Configurações finais do i-Educar
Após instalar as dependências necessárias, restaurar o banco de dados, clonar código fonte e configurar o apache, é necessário finalizar as configurações do i-Educar, para isto, basta editar o arquivo de configurações:
nano ~/ieducar/ieducar/configuration/ieducar.ini

Recaptcha
O recaptcha é exibido na redefinição de senha e após várias tentativas de logins sem sucesso.
Crie uma conta para o recaptcha em http://google.com/recaptcha/admin/create
marcando a opção para usar a chave em todos domínios. 
Após criar a conta, definir no arquivo de configuração as chaves recaptcha (sem aspas)
app.recaptcha.public_key  = ...
app.recaptcha.private_key = ...

Login
Após salvar as configurações, o login pode ser feito na aplicação acessando http://ieducar.local/ com usuário e senha admin.
Migrações
Devem ser executados todos sqls dos arquivos de migrações encontrados em ~/ieducar/ieducar/misc/database/deltas/portabilis/ a partir do número 53. Isso pode ser feito através da execução do seguinte script:
curl -L https://gist.githubusercontent.com/lucassch/9324434/raw/test_migrations_53_54_55_56_57.sh | bash

Inicialização cluster postgresql
A cada inicialização do sistema operacional, o cluster do postgres precisará ser inicializado executando:
bash ~/ieducar/ieducar/scripts/db.sh start 

Da mesma maneira, o cluster pode ser parado executando:
bash ~/ieducar/ieducar/scripts/db.sh stop 

Caso o cluster não seja inicializado, o banco de dados estará offline, impedindo que a aplicação seja acessada.
Para inicializar o cluster do banco de dados junto com o sistema operacional, basta adicionar um job, ex:

Primeiro abra o crontab:
crontab -e 

Em seguida adicione ao final do arquivo esta linha:
@reboot ~/ieducar/ieducar/scripts/db.sh start
Após salvar o crontab, o cluster do banco de dados deve ser automaticamente iniciado junto com sistema operacional.


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
