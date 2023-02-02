# Docker

### Dependência

Para executar o projeto é necessário a utilização de alguns softwares para
facilitar o desenvolvimento.

- [Git](https://git-scm.com/downloads)
- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Instalação utilizando Docker

> ATENÇÃO: Essa forma de instação tem o objetivo de facilitar demonstrações e desenvolvimento!

Para instalar o projeto execute **todos os passos** abaixo.

Clone o repositório:

```bash
git clone https://github.com/portabilis/i-educar.git && cd i-educar
```

Faça o build das imagens Docker utilizadas no projeto (pode levar alguns minutos) e inicie os containers da aplicação:

```bash
docker-compose up -d --build
```

Execute o comando para fazer uma nova instalação:

```bash
docker-compose exec php composer new-install
```

#### Primeiro acesso

Após finalizada a instalação, descubra em qual endereço o i-Educar está rodando, basta executar o comando:

```bash
docker-compose port nginx 80
```

Acesse o endereço que será exibido após rodar o comando acima.

O usuário padrão é: `admin` / A senha padrão é: `123456789`

Assim que realizar seu primeiro acesso **não se esqueça de alterar a senha padrão**.

#### Personalizando a instalação

Você pode criar um arquivo `docker-compose.override.yml` para personalizar sua instalação do i-Educar, mudando as portas dos serviços ou o mapeamento dos volumes extras para a aplicação.

Você também pode realizar modificações nos arquivos de configurações disponível dentro de `docker/` para atender a características específicas do ambiente.

#### Xdebug

A ferramenta [Xdebug](https://xdebug.org/) está incluída no projeto com o intuito de facilitar o processo de debug durante o desenvolvimento. Para configurá-la, modifique os valores das variáveis `XDEBUG_*` no arquivo `docker-compose.override.yml` conforme orientações da sua IDE de desenvolvimento.

#### Executando testes unitários

Para rodar os testes, é necessário ter o i-Educar rodando e com uma base limpa, apenas a estrutura inicial e as migrations, crie o arquivo de configuração:

```bash
cp .env.example .env.testing
```

Execute o comando:

```bash
docker-compose exec php vendor/bin/phpunit
```

### Instalação do pacote de relatórios:

A instalação do pacote de relatórios realizada no processo de build das imagens docker ainda não está disponível, no entanto, pode ser instalado manualmente dentro do container, conforme: [Instalação do pacote de relatórios](#instalação-do-pacote-de-relatórios)


Fluxo a ser desenvolvido para geração de imagens docker do i-Educar contemplanto o pacote de relatórios:

1 - Criar uma variável para ser passada no docker-compose

2 - Definir um bloco condicional dentro do Dockerfile para realizar ou não a instalação do pacote de relatórios
***

## Perguntas frequentes (FAQ)

Algumas perguntas aparecem recorrentemente. Olhe primeiro por aqui: [FAQ](https://github.com/portabilis/i-educar-website/blob/master/docs/faq.md).

---

Powered by [Portabilis Tecnologia](http://www.portabilis.com.br/).
