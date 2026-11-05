# 12. Upload e Gestão de Arquivos

**Módulo:** Upload e Gestão de Arquivos  
**Aplicação:** i-Educar (Módulo Web)  
**Tipo de teste:** Manual funcional  
**Ambiente:** Homologação  
**Base:** regras-de-negocio.md  
**Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-079 — Upload de arquivo válido vinculado a processo

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui permissão para envio de arquivos    |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Acessar a funcionalidade de upload de arquivos.
2. Selecionar um arquivo válido.
3. Associar o arquivo a um processo do sistema.
4. Confirmar o envio.

**Resultado esperado:** Sistema realiza o upload com sucesso e gera um identificador único para o arquivo.

---

## CT-080 — Tentativa de upload sem selecionar arquivo

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui acesso à funcionalidade de upload    |
| **Técnica**      | Particionamento de Equivalência — classe inválida |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Acessar a tela de upload.
2. Não selecionar nenhum arquivo.
3. Confirmar o envio.

**Resultado esperado:** Sistema bloqueia a operação e informa que é necessário selecionar um arquivo.

---

## CT-081 — Upload de arquivo com formato não permitido

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Sistema possui restrição de formatos de arquivos   |
| **Técnica**      | Particionamento de Equivalência — classe inválida |
| **Prioridade**   | Média                                             |

**Passos:**

1. Acessar a funcionalidade de upload.
2. Selecionar arquivo com formato não permitido.
3. Confirmar o envio.

**Resultado esperado:** Sistema rejeita o arquivo e informa que o formato não é permitido.

---

## CT-082 — Visualização de arquivo previamente enviado

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Existe arquivo armazenado no sistema               |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Média                                             |

**Passos:**

1. Localizar um arquivo previamente enviado.
2. Solicitar visualização do arquivo.

**Resultado esperado:** Sistema apresenta o arquivo corretamente ao usuário.

---

## CT-083 — Download de arquivo armazenado

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Existe arquivo disponível para download            |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Média                                             |

**Passos:**

1. Localizar um arquivo armazenado.
2. Solicitar download.
3. Abrir o arquivo baixado.

**Resultado esperado:** Arquivo é baixado corretamente mantendo sua integridade e conteúdo original.

---

## CT-084 — Geração de identificador único em uploads simultâneos

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Dois usuários realizando upload simultaneamente    |
| **Técnica**      | Análise de Valor Limite — concorrência            |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Realizar dois uploads simultâneos do mesmo tipo de arquivo.
2. Verificar os identificadores gerados.
3. Consultar os arquivos utilizando os identificadores.

**Resultado esperado:** Sistema gera identificadores únicos para cada arquivo, sem sobrescrever registros existentes.

---

## CT-085 — Recuperação de arquivo por diferentes módulos do sistema

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Arquivo enviado e referenciado por diferentes módulos |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Realizar upload de um arquivo.
2. Associar o arquivo a um processo específico.
3. Acessar o processo por outro módulo do sistema.
4. Solicitar recuperação do arquivo.

**Resultado esperado:** Sistema recupera corretamente o arquivo utilizando o identificador associado.

---

## CT-086 — Tentativa de recuperar arquivo inexistente

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Identificador de arquivo não existe no sistema     |
| **Técnica**      | Particionamento de Equivalência — classe inválida |
| **Prioridade**   | Baixa                                             |

**Passos:**

1. Informar um identificador de arquivo inexistente.
2. Solicitar recuperação.

**Resultado esperado:** Sistema informa que o arquivo não foi encontrado e não apresenta erro inesperado.
