# 6.0 Avaliação e Boletim

**Módulo:** Avaliação e Boletim
**Aplicação:** i-Educar (Módulo Web)
**Tipo de teste:** Manual funcional
**Ambiente:** Homologação
**Base:** [regras-de-negocio.md](../regras-de-negocio.md)
**Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são
> fictícios, criados especificamente para fins de teste.

---

## CT-033 — Lançamento de nota dentro do período liberado

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Período de lançamento configurado (ex.: 01/04/2026 a 30/04/2026) e data atual dentro do intervalo |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**
1. Acessar o lançamento de notas dentro do período liberado
2. Lançar uma nota para um aluno
3. Salvar

**Resultado esperado:** Nota salva com sucesso.

---

## CT-034 — Lançamento de nota no primeiro dia do período liberado (limite)

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Período configurado com início em 01/04/2026 |
| **Técnica** | BVA — limite inferior válido |
| **Prioridade** | Alta |

**Passos:**
1. Com a data do sistema/simulada em 01/04/2026, lançar uma nota

**Resultado esperado:** Lançamento aceito normalmente.

---

## CT-035 — Lançamento de nota um dia antes do período liberado

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Período configurado com início em 01/04/2026 |
| **Técnica** | BVA — abaixo do limite (inválido) |
| **Prioridade** | Alta |

**Passos:**
1. Com a data do sistema/simulada em 31/03/2026, tentar lançar uma nota

**Resultado esperado:** Sistema bloqueia o lançamento, informando que o
período ainda não está liberado.

---

## CT-036 — Lançamento de nota no último dia do período liberado (limite)

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Período configurado com término em 30/04/2026 |
| **Técnica** | BVA — limite superior válido |
| **Prioridade** | Alta |

**Passos:**
1. Com a data do sistema/simulada em 30/04/2026, lançar uma nota

**Resultado esperado:** Lançamento aceito normalmente.

---

## CT-037 — Lançamento de nota um dia após o período liberado

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Período configurado com término em 30/04/2026 |
| **Técnica** | BVA — acima do limite (inválido) |
| **Prioridade** | Alta |

**Passos:**
1. Com a data do sistema/simulada em 01/05/2026, tentar lançar uma nota

**Resultado esperado:** Sistema bloqueia o lançamento, informando que o
período já foi encerrado.

---

## CT-038 — Arredondamento de nota no valor exato do corte (limite)

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Tabela de arredondamento configurada com regra: nota ≥ 6,95 arredonda para 7,0 |
| **Técnica** | BVA — limite exato de arredondamento para cima |
| **Prioridade** | Média |

**Passos:**
1. Lançar uma nota bruta de 6,95 para um aluno
2. Consultar a nota final calculada/exibida no boletim

**Resultado esperado:** Nota exibida como 7,0, conforme a tabela configurada.

---

## CT-039 — Nota logo abaixo do valor de corte de arredondamento

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Tabela de arredondamento configurada com regra: nota ≥ 6,95 arredonda para 7,0 |
| **Técnica** | BVA — imediatamente abaixo do limite (não deve arredondar) |
| **Prioridade** | Média |

**Passos:**
1. Lançar uma nota bruta de 6,94 para um aluno
2. Consultar a nota final calculada/exibida no boletim

**Resultado esperado:** Nota exibida como 6,9 (ou o valor correspondente sem
o arredondamento para 7,0), confirmando que o corte configurado está sendo
respeitado com precisão.

---

## CT-040 — Alteração em lote do tipo de boletim aplicado a turmas

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Conjunto de turmas selecionado, com tipo de boletim atual diferente do desejado |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**
1. Selecionar as turmas desejadas
2. Executar a alteração em lote para o novo tipo de boletim
3. Confirmar a operação

**Resultado esperado:** Todas as turmas selecionadas passam a usar o novo
tipo de boletim; o resultado da operação em lote fica disponível para
conferência (sucesso/erro por turma).
