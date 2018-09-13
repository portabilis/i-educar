---
id: dev-padroes-docs
title: Padrões de documentação
---

# Documentação

Foram definidos alguns padrões para que a documentação possa evoluir colaborativamente.

## Padrões definidos

As definições tiveram como base a evolução do projeto, evitando obstáculos para isto.

- Para cada *perfil de usuário* será criado um padrão de nomeação de arquivos
    - Administrador (**admin**-*funcionalidade*.md)
    - Usuário (**user**-*funcionalidade*.md)
    - Desenvolvedor (**dev**-*funcionalidade*.md)
- **Commits** e **Pull Requests** serão realizados em **Português** (PT-BR)

## Ferramenta para documentação

Foi adotada uma ferramenta que tivesse alguns requisitos:
- Código aberto
- Boa aceitação pela comunidade
- Usabilidade simplificada
- Tecnologia simples
- Versionamento da documentação, seguindo versões da aplicação

A ferramenta escolhida foi [Docusaurus](https://docusaurus.io).

### Vantagens do Docusaurus

- Utiliza **Markdown**
- Conversão do conteúdo Markdown para HTML estático
- Construída em **React** (biblioteca JavaScript)
- Boa aceitação da comunidade
- Pública conteúdo estático diretamente no GitHub Pages (não há custos de infraestrutura tecnológica)
- Versionamento da documentação, seguindo versões da aplicação