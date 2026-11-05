# 4.0 Enturmação (Vínculo Aluno-Turma)

**Módulo:** Enturmação
**Aplicação:** i-Educar (Módulo Web)
**Tipo de teste:** Manual funcional
**Ambiente:** Homologação
**Base:** [regras-de-negocio.md](../regras-de-negocio.md)
**Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são
> fictícios, criados especificamente para fins de teste.
>
> Este é o módulo com mais regras de negócio combinadas do documento de
> referência, por isso recebe cobertura mais ampla, incluindo o teste de
> atomicidade em operações de lote.

---

## CT-020 — Enturmar aluno em turma com vaga disponível

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Turma com capacidade > número de alunos enturmados |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**
1. Selecionar aluno matriculado e sem enturmação conflitante
2. Enturmá-lo na turma com vaga disponível

**Resultado esperado:** Enturmação realizada com sucesso.

---

## CT-021 — Enturmar aluno na última vaga disponível (limite)

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Turma com exatamente 1 vaga restante |
| **Técnica** | BVA — limite superior válido (última vaga) |
| **Prioridade** | Alta |

**Passos:**
1. Enturmar um aluno na turma que possui exatamente 1 vaga restante

**Resultado esperado:** Enturmação aceita; turma passa a constar com 0 vagas
disponíveis.

---

## CT-022 — Enturmar aluno em turma sem vaga (lotada)

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Turma com 0 vagas disponíveis |
| **Técnica** | BVA — acima do limite (inválido) |
| **Prioridade** | Alta |

**Passos:**
1. Tentar enturmar um aluno em uma turma com 0 vagas disponíveis

**Resultado esperado:** Sistema bloqueia a operação e informa que não há
vaga disponível.

---

## CT-023 — Enturmar aluno com conflito de horário

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Aluno já enturmado em uma turma com horário/período coincidente com a nova turma |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**
1. Selecionar aluno já enturmado em uma turma no período matutino, por exemplo
2. Tentar enturmá-lo em outra turma com o mesmo horário/período

**Resultado esperado:** Sistema bloqueia a segunda enturmação por conflito
de horário.

---

## CT-024 — Desenturmar com data anterior à data de enturmação

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Aluno enturmado com data de enturmação = 10/03/2026 |
| **Técnica** | BVA — abaixo do limite (inválido) |
| **Prioridade** | Alta |

**Passos:**
1. Tentar registrar a desenturmação com data = 09/03/2026 (um dia antes da
   data de enturmação)

**Resultado esperado:** Sistema bloqueia, pois a data de desenturmação deve
ser posterior à data de enturmação.

---

## CT-025 — Desenturmar com data imediatamente posterior à enturmação (limite)

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Aluno enturmado com data de enturmação = 10/03/2026 |
| **Técnica** | BVA — limite inferior válido |
| **Prioridade** | Alta |

**Passos:**
1. Registrar a desenturmação com data = 11/03/2026 (primeiro dia válido após
   a enturmação)

**Resultado esperado:** Desenturmação aceita normalmente.

---

## CT-026 — Usuário com permissão de enturmar, mas sem permissão de desenturmar

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Usuário de teste com permissão de enturmar e sem permissão de desenturmar |
| **Técnica** | Particionamento de Equivalência — classes de permissão assimétricas |
| **Prioridade** | Alta |

**Passos:**
1. Logar com o usuário configurado
2. Enturmar um aluno (deve funcionar)
3. Tentar desenturmar o mesmo aluno (deve ser bloqueado)

**Resultado esperado:** A enturmação é permitida e a desenturmação é
bloqueada, confirmando que são permissões independentes.

---

## CT-027 — Remanejamento de aluno entre turmas

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Usuário com permissão específica de remanejamento; aluno enturmado na Turma A; Turma B com vaga |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**
1. Executar o remanejamento do aluno da Turma A para a Turma B pela função
   específica (não via desenturmar + enturmar manualmente)

**Resultado esperado:** Aluno passa a constar na Turma B e não mais na Turma
A, com o registro identificado como remanejamento (não como duas operações
separadas).

---

## CT-028 — Atomicidade da enturmação em lote com falha no meio do processo

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Lista de 5 alunos para enturmação em lote, sendo que 1 deles gera uma validação inválida (ex.: turma sem vaga para ele) |
| **Técnica** | Particionamento de Equivalência — classe inválida (integridade transacional) |
| **Prioridade** | Alta |

**Passos:**
1. Selecionar os 5 alunos para enturmação em lote
2. Confirmar o processamento, sabendo que um deles falhará na validação

**Resultado esperado:** Nenhum dos 5 alunos deve ficar parcialmente
enturmado — a operação é atômica: ou todos que passam nas validações são
gravados, ou nenhuma alteração é persistida, conforme o comportamento
transacional documentado.
