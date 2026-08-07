# 10. Comunicação (Avisos e Notificações)

**Módulo:** Comunicação (Avisos e Notificações)  
**Aplicação:** i-Educar (Módulo Web)  
**Tipo de teste:** Manual funcional  
**Ambiente:** Homologação  
**Base:** regras-de-negocio.md  
**Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-062 — Publicação de aviso comunicado para usuários

| **CampoDetalhe** |                                                 |
| ---------------- | ----------------------------------------------- |
| **Pré-condição** | Usuário administrador com permissão de comunicação |
| **Técnica**      | Particionamento de Equivalência — classe válida |
| **Prioridade**   | Alta                                            |

**Passos:**

1. Acessar o módulo de comunicação.
2. Criar um novo aviso.
3. Informar título e conteúdo da mensagem.
4. Selecionar os usuários destinatários.
5. Publicar o aviso.

**Resultado esperado:** Aviso publicado com sucesso e disponibilizado aos usuários selecionados.

---

## CT-063 — Tentativa de publicar aviso sem conteúdo obrigatório

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário administrador com acesso ao módulo        |
| **Técnica**      | Particionamento de Equivalência — classe inválida |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Acessar a criação de aviso.
2. Informar apenas o título.
3. Deixar o conteúdo vazio.
4. Tentar publicar.

**Resultado esperado:** Sistema impede a publicação e informa que o conteúdo do aviso é obrigatório.

---

## CT-064 — Edição de aviso já publicado

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Existe aviso publicado no sistema                 |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Média                                             |

**Passos:**

1. Localizar um aviso publicado.
2. Editar o conteúdo do aviso.
3. Salvar as alterações.

**Resultado esperado:** Sistema atualiza o aviso e mantém a nova versão disponível aos usuários.

---

## CT-065 — Bloqueio de navegação até leitura de aviso obrigatório

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui aviso obrigatório pendente de leitura |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Acessar o sistema com usuário que possui aviso obrigatório pendente.
2. Tentar acessar outra funcionalidade.
3. Tentar acessar uma URL direta para outra tela.

**Resultado esperado:** Sistema impede a navegação até que o usuário confirme a leitura do aviso obrigatório.

---

## CT-066 — Confirmação de leitura de aviso obrigatório

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui aviso obrigatório pendente          |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Alta                                              |

**Passos:**

1. Abrir o aviso obrigatório.
2. Ler o conteúdo apresentado.
3. Confirmar a leitura.
4. Acessar outras funcionalidades do sistema.

**Resultado esperado:** Sistema registra a confirmação de leitura e libera o acesso normalmente.

---

## CT-067 — Listagem de notificações do usuário logado

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui notificações cadastradas            |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Média                                             |

**Passos:**

1. Acessar o painel de notificações.
2. Consultar a lista apresentada.
3. Verificar quantidade de notificações não lidas.

**Resultado esperado:** Sistema apresenta as notificações do usuário e informa corretamente a quantidade de itens não lidos.

---

## CT-068 — Marcação individual de notificação como lida

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui notificações não lidas              |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Média                                             |

**Passos:**

1. Abrir uma notificação não lida.
2. Marcar a notificação como lida.
3. Atualizar a tela.

**Resultado esperado:** Notificação fica marcada como lida e o contador de não lidas é atualizado corretamente.

---

## CT-069 — Marcação de todas as notificações como lidas

| **CampoDetalhe** |                                                   |
| ---------------- | ------------------------------------------------- |
| **Pré-condição** | Usuário possui múltiplas notificações não lidas    |
| **Técnica**      | Particionamento de Equivalência — classe válida   |
| **Prioridade**   | Média                                             |

**Passos:**

1. Acessar o painel de notificações.
2. Selecionar a opção "Marcar todas como lidas".
3. Atualizar a página.
4. Verificar o contador.

**Resultado esperado:** Todas as notificações são marcadas como lidas e o contador de não lidas apresenta valor zero.
