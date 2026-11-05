# 11. Configurações e Administração

**Módulo:** Configurações e Administração  
**Aplicação:** i-Educar (Módulo Web)  
**Tipo de teste:** Manual funcional  
**Ambiente:** Homologação  
**Base:** regras-de-negocio.md  
**Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-070 — Configuração de parâmetros gerais do sistema por instituição

| **CampoDetalhe** |                                                 |
| ---------------- | ----------------------------------------------- |
| **Pré-condição** | Usuário administrador com permissão de configuração |
| **Técnica**      | Particionamento de Equivalência — classe válida |
| **Prioridade**   | Alta                                            |

**Passos:**

1. Acessar o módulo de configurações.
2. Selecionar os parâmetros gerais da instituição.
3. Alterar um parâmetro existente.
4. Salvar as alterações.

**Resultado esperado:** Sistema salva as configurações e aplica os novos parâmetros para a instituição selecionada.

---

## CT-071 — Tentativa de alterar configuração sem permissão

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário sem permissão de configuração administrativa |
| **Técnica**      | Particionamento de Equivalência — classe inválida |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Acessar o módulo de configurações.
2. Tentar alterar um parâmetro do sistema.
3. Confirmar a operação.

**Resultado esperado:** Sistema bloqueia a alteração e informa que o usuário não possui permissão necessária.

---

## CT-072 — Realização de backup dos dados com permissão válida

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui permissão específica de backup      |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Acessar a funcionalidade de backup.
2. Solicitar geração do backup.
3. Aguardar processamento.
4. Realizar download do arquivo gerado.

**Resultado esperado:** Backup gerado com sucesso e disponibilizado para download.

---

## CT-073 — Tentativa de backup por administrador sem permissão específica

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui perfil administrativo, porém sem permissão de backup |
| **Técnica**      | Particionamento de Equivalência — classe inválida |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Acessar a opção de backup.
2. Tentar executar a operação.

**Resultado esperado:** Sistema bloqueia a operação, mesmo o usuário possuindo perfil de administrador geral.

---

## CT-074 — Exportação de dados gerais do sistema

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui permissão específica de exportação |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Acessar a funcionalidade de exportação de dados.
2. Selecionar os dados desejados.
3. Confirmar a exportação.

**Resultado esperado:** Sistema gera o arquivo de exportação contendo os dados selecionados.

---

## CT-075 — Exportação de documentos e arquivos

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Existem documentos armazenados no sistema          |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Média                                             |

**Passos:**

1. Acessar a opção de exportação de documentos.
2. Selecionar arquivos disponíveis.
3. Solicitar exportação.

**Resultado esperado:** Sistema gera o pacote de documentos selecionados corretamente.

---

## CT-076 — Alteração em lote de componentes curriculares com prévia

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Existem cursos, séries e componentes cadastrados |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Acessar gerenciamento de componentes curriculares.
2. Selecionar múltiplos registros.
3. Executar operação em lote.
4. Validar a tela de prévia apresentada.
5. Confirmar a execução definitiva.

**Resultado esperado:** Sistema apresenta a prévia das alterações antes da execução e aplica as modificações somente após confirmação.

---

## CT-077 — Cadastro de tipo/perfil de usuário com permissões

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário administrador autorizado                   |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Média                                             |

**Passos:**

1. Acessar gerenciamento de usuários e permissões.
2. Criar um novo perfil.
3. Associar permissões específicas.
4. Salvar o cadastro.

**Resultado esperado:** Novo perfil criado com as permissões configuradas corretamente.

---

## CT-078 — Tentativa de exclusão de perfil vinculado a usuários ativos

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Perfil associado a usuários ativos                 |
| **Técnica**      | Particionamento de Equivalência — classe inválida |
| **Prioridade**   | Média                                             |

**Passos:**

1. Acessar gerenciamento de perfis.
2. Selecionar perfil utilizado por usuários.
3. Solicitar exclusão.

**Resultado esperado:** Sistema impede a exclusão ou informa que existem vínculos ativos impedindo a remoção.
