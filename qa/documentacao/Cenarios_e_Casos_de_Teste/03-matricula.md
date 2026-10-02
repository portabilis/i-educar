# 3.0 Matrícula

**Módulo:** Matrícula
**Aplicação:** i-Educar (Módulo Web)
**Tipo de teste:** Manual funcional
**Ambiente:** Homologação
**Base:** [regras-de-negocio.md](../regras-de-negocio.md)
**Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são
> fictícios, criados especificamente para fins de teste.

---

## CT-013 — Matrícula com data no primeiro dia do ano letivo (limite inferior)

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Ano letivo configurado (ex.: 01/02/2026 a 20/12/2026) |
| **Técnica** | BVA — limite inferior válido |
| **Prioridade** | Alta |

**Passos:**
1. Efetuar matrícula de aluno com data de entrada = 01/02/2026 (primeiro dia
   do ano letivo)

**Resultado esperado:** Matrícula aceita normalmente.

---

## CT-014 — Matrícula com data um dia antes do início do ano letivo

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Ano letivo configurado (ex.: 01/02/2026 a 20/12/2026) |
| **Técnica** | BVA — abaixo do limite inferior (inválido) |
| **Prioridade** | Alta |

**Passos:**
1. Tentar efetuar matrícula com data de entrada = 31/01/2026 (um dia antes
   do início do ano letivo)

**Resultado esperado:** Sistema bloqueia a matrícula e informa que a data
está fora do período letivo.

---

## CT-015 — Matrícula com data no último dia do ano letivo (limite superior)

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Ano letivo configurado (ex.: 01/02/2026 a 20/12/2026) |
| **Técnica** | BVA — limite superior válido |
| **Prioridade** | Alta |

**Passos:**
1. Efetuar matrícula com data de entrada = 20/12/2026 (último dia do ano letivo)

**Resultado esperado:** Matrícula aceita normalmente.

---

## CT-016 — Matrícula com data um dia após o fim do ano letivo

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Ano letivo configurado (ex.: 01/02/2026 a 20/12/2026) |
| **Técnica** | BVA — acima do limite superior (inválido) |
| **Prioridade** | Alta |

**Passos:**
1. Tentar efetuar matrícula com data de entrada = 21/12/2026 (um dia após o
   fim do ano letivo)

**Resultado esperado:** Sistema bloqueia a matrícula e informa que a data
está fora do período letivo.

---

## CT-017 — Matrícula conflitante com matrícula ativa existente

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Aluno já possui matrícula ativa em um período que se sobrepõe ao da nova tentativa |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**
1. Selecionar um aluno com matrícula ativa
2. Tentar efetuar uma nova matrícula com data conflitante com a já existente

**Resultado esperado:** Sistema impede a nova matrícula, indicando conflito
com uma matrícula já ativa.

---

## CT-018 — Alteração de situação da matrícula sem permissão

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Usuário sem a permissão específica de alterar situação de matrícula |
| **Técnica** | Particionamento de Equivalência — classe inválida (permissão) |
| **Prioridade** | Alta |

**Passos:**
1. Logar com usuário sem a permissão específica
2. Tentar alterar a situação de uma matrícula (ex.: para "cancelada")

**Resultado esperado:** Ação bloqueada; opção deve estar oculta/desabilitada
ou, se forçada via requisição direta, rejeitada pelo backend.

---

## CT-019 — Reclassificação de matrícula

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Aluno com matrícula ativa em uma série |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**
1. Acessar a matrícula do aluno
2. Executar reclassificação para outra série/etapa
3. Confirmar

**Resultado esperado:** Série/etapa atualizada sem gerar uma nova matrícula
(mesma matrícula original é preservada com o rastro da reclassificação).
