# 2.0 Cadastros Gerais (Pessoas, Escolas, Turmas)

**Módulo:** Cadastros Gerais
**Aplicação:** i-Educar (Módulo Web)
**Tipo de teste:** Manual funcional
**Ambiente:** Homologação
**Base:** [regras-de-negocio.md](../regras-de-negocio.md)
**Técnicas aplicadas:** Particionamento de Equivalência (PE)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são
> fictícios, criados especificamente para fins de teste.

---

## CT-008 — Cadastro de pessoa com campos obrigatórios do Censo preenchidos

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Usuário logado com permissão de cadastro |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**
1. Acessar cadastro de pessoa
2. Preencher todos os campos obrigatórios, incluindo endereço completo,
   documentação, raça/cor e deficiência
3. Salvar o cadastro

**Resultado esperado:** Cadastro salvo com sucesso e apto para exportação ao
Censo Escolar (sem pendências relacionadas a esses campos).

---

## CT-009 — Cadastro de pessoa com campo obrigatório do Censo ausente

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Usuário logado com permissão de cadastro |
| **Técnica** | Particionamento de Equivalência — classe inválida (campo ausente) |
| **Prioridade** | Alta |

**Passos:**
1. Acessar cadastro de pessoa
2. Preencher todos os campos, deixando "raça/cor" em branco
3. Salvar o cadastro

**Resultado esperado:** Sistema permite salvar o cadastro (campo não bloqueia
o cadastro em si), mas o registro deve aparecer como pendência na validação
de exportação do Censo Escolar.

---

## CT-010 — Exclusão de cadastro (exclusão lógica)

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Existe um cadastro de pessoa/escola/turma ativo |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**
1. Excluir um cadastro pela interface
2. Verificar se ele some da listagem padrão
3. Consultar o registro por uma via que exiba inativos (histórico, relatório, banco)

**Resultado esperado:** O registro desaparece da listagem ativa, mas
permanece íntegro e recuperável — a exclusão é lógica (inativação), não física.

---

## CT-011 — Cadastro de pessoa marcada como falecida

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Cadastro de pessoa existente, sem matrícula ativa |
| **Técnica** | Particionamento de Equivalência — classe inválida (regra de negócio) |
| **Prioridade** | Alta |

**Passos:**
1. Marcar o cadastro da pessoa como falecida
2. Tentar efetuar uma nova matrícula para essa pessoa

**Resultado esperado:** Sistema bloqueia a nova matrícula e sinaliza que o
cadastro está marcado como falecido.

---

## CT-012 — Cadastro de calendário letivo com evento válido

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Ano letivo cadastrado para a instituição |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**
1. Acessar cadastro de calendário letivo
2. Inserir um feriado/evento com data dentro do ano letivo vigente
3. Salvar

**Resultado esperado:** Evento salvo com sucesso e refletido no calendário
da instituição.
