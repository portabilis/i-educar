---
id: dev-comecando-contribuir-docs
title: Começando a contribuir com a documentação
---

## Compreendendo o Docusaurus

[Docusaurus](https://docusaurus.io/) é uma aplicação instalada localmente em cada computador, que facilita o processo de documentação. Ao iniciar o Docusaurus localmente, ele abre um servidor local com porta padrão 3000.

Ao iniciar o servidor local, o Docusaurus compila os arquivos Markdown, para converter em HTML e gerar a visualização no servidor local.

### Respostas rápidas sobre o Docusaurus

- O Docusaurus fica alocado no repositório?
    - Não. O Docusaurus apenas deixa os módulos principais no repositório, mas a aplicação em geral precisa ser instalada do computador do usuário que vai contribuir com a documentação.

* O Docusaurus precisa ser instalado na máquina do usuário final que está consultando a documentação?
    * Não é necessário, pois são gerados HTML estáticos e são inclusos na *branch* **gh-pages** para que seja exibido como um projeto web.

## Como utilizar o Docusaurus localmente

- Instale as dependências do Docusaurus (primeiro passo): https://docusaurus.io/docs/en/installation.html
    - Instalação do **Node.js**: https://nodejs.org/en/download/
    - Instalação do **Yarn**: https://yarnpkg.com/en/docs/install
- Faça o **fork** do projeto no seu usuário do GitHub
- Clone o projeto: `git clone https://github.com/[SEU-USUARIO]/i-educar`
- Acesse o diretório *website* do projeto: `cd i-educar/website`
- Adicione o *Docusaurus* ao projeto: `yarn add docusaurus --latest`
- Inicie o serviço do Docusaurus na sua máquina: `yarn start`

## 1, 2, 3... Documentando!

### Colocando a mão na massa

Para que a documentação seja criada, é preciso compreender como o Docusaurus funciona, para que ele possa trabalhar com as suas informações. Explicarei o básico para que isso aconteça, mas se desejar aprofundar o conhecimento, veja a seção [Mesa de ferramentas (guias)](#mesa-ferramentas) abaixo.

#### Criando uma nova página de documentação

- Navegue até o diretório *docs* do projeto: `cd i-educar/docs`
- Crie um arquivo com a extensão `.md`
    - Veja o padrão de nomeação do arquivo ao criá-lo: [Padrões de documentação - Padrões definidos](dev-padroes-docs.md#padrões-definidos)
- Insira o cabeçalho no arquivo para que seja processado pelo Docusaurus
```bash
---
id: [PERFIL]-funcionalidade
title: Título da página
sidebar_label: Nome que será exibido no menu lateral esquerdo
---
```
> *sidebar_label* é um informação opcional. Se for omitida, assumirá o valor de *title*.

- Edite o arquivo **i-educar/website/sidebars.json** e inclua o **id** definido no cabeçalho do arquivo
- Inicie o Docusaurus:
    - `cd i-educar/website`
    - `yarn start`

### <a class="anchor" aria-hidden="true" id="mesa-ferramentas"></a> Mesa de ferramentas (guias)
- [Documentação Docusaurus (em Inglês)](https://docusaurus.io/docs/en/installation)
- [Guia Markdown (em Inglês)](https://www.markdownguide.org/)

## Publicando alterações de documentação no GitHub Pages

Dependẽncias para executar este procedimento:
1. Docusaurus instalado e funcional
1. Permissão de commit na branch **gh-pages** do repositório desejado

- Acesse o diretório *website* do projeto: `cd i-educar/website`
- Realize o build e publique na branch **gh-pages**: `GIT_USER=[SEU-USUARIO] CURRENT_BRANCH=[BRANCH-ORIGEM] yarn run publish-gh-pages`

> As instruções **GIT_USER** e **CURRENT_BRANCH** são variáveis de ambiente utilizadas pelo Docusaurus para realizar a publicação no GitHub Pages.

## Como eu posso ajudar?

Problemas mais comuns de documentações:
- Erros de digitação
- Coompreensão da informação (texto confuso)
- Ausência de processos documentados
- Falta de padrões ([Padrões de documentação](dev-padroes-docs.md))

Tendo em vista estes problemas, se estiver lendo a documentação e encontrar algum erro de digitação, informação difícil de compreender, faltou a documentação de algum processo ou está despadronizado, **nos ajude e contribua**!

### Ainda não sei perfeitamente como ajudar, o que eu faço?

Abra um tópico no fórum do projeto **i-Educar** e compartilhe a sua dificuldade para que você seja capaz de ajudar. Ser capaz de ajudar é importante para todos!

> **Fique atento:** A sua necessidade de ajuda pode se tornar parte de nossa documentação, então não perca a oportunidade de ajudar!