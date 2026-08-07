# 8.0 Matrícula

**Módulo:** Matrícula **Aplicação:** i-Educar (Módulo Web) **Tipo de teste:** Manual funcional **Ambiente:** Homologação **Base:** regras-de-negocio.md **Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-048 — Matrícula de aluno em turma com vagas disponíveis

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Aluno ativo e turma com vagas disponíveis |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar o módulo de matrícula.
2. Selecionar um aluno ativo.
3. Escolher uma turma com vagas.
4. Confirmar a matrícula.

**Resultado esperado:** Matrícula realizada com sucesso.

---

## CT-049 — Matrícula em turma sem vagas

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Turma com número máximo de vagas preenchido |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar o módulo de matrícula.
2. Selecionar um aluno.
3. Escolher uma turma sem vagas disponíveis.
4. Confirmar a matrícula.

**Resultado esperado:** Sistema impede a matrícula e informa que não há vagas disponíveis.

---

## CT-050 — Matrícula no limite máximo de vagas da turma

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Turma com apenas uma vaga restante |
| **Técnica** | BVA — limite superior válido |
| **Prioridade** | Alta |

**Passos:**

1. Selecionar uma turma com apenas uma vaga disponível.
2. Matricular um aluno.

**Resultado esperado:** Matrícula realizada com sucesso e a turma passa a não possuir vagas disponíveis.

---

## CT-051 — Tentativa de matrícula excedendo o limite de vagas

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Turma já atingiu o limite máximo de alunos |
| **Técnica** | BVA — acima do limite (inválido) |
| **Prioridade** | Alta |

**Passos:**

1. Tentar matricular outro aluno na turma.

**Resultado esperado:** Sistema bloqueia a matrícula por limite de vagas atingido.

---

## CT-052 — Cancelamento de matrícula

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Aluno com matrícula ativa |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Localizar uma matrícula ativa.
2. Selecionar a opção de cancelamento.
3. Confirmar a operação.

**Resultado esperado:** Matrícula cancelada com sucesso e situação atualizada no sistema.

---

## CT-053 — Reativação de matrícula cancelada

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Matrícula previamente cancelada |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Localizar uma matrícula cancelada.
2. Selecionar a opção de reativação.
3. Confirmar a operação.

**Resultado esperado:** Matrícula reativada com sucesso.

---

## CT-054 — Transferência de aluno entre turmas

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Aluno matriculado e turma de destino com vagas |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Localizar a matrícula do aluno.
2. Selecionar a opção de transferência.
3. Escolher a turma de destino.
4. Confirmar a operação.

**Resultado esperado:** Aluno transferido para a nova turma e matrícula atualizada com sucesso.
