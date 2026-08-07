# 9.0 Quadro de Horários

**Módulo:** Quadro de Horários **Aplicação:** i-Educar (Módulo Web) **Tipo de teste:** Manual funcional **Ambiente:** Homologação **Base:** regras-de-negocio.md **Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-055 — Cadastro de horário sem conflito

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Professor, turma e sala disponíveis no horário selecionado |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar o cadastro de quadro de horários.
2. Selecionar uma turma.
3. Informar professor, disciplina, sala e horário disponível.
4. Salvar.

**Resultado esperado:** Horário cadastrado com sucesso.

---

## CT-056 — Cadastro de horário com conflito de professor

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Professor já possui aula cadastrada no mesmo dia e horário |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Tentar cadastrar uma aula para um professor já alocado no mesmo horário.
2. Salvar.

**Resultado esperado:** Sistema bloqueia o cadastro informando conflito de horário do professor.

---

## CT-057 — Cadastro de horário com conflito de sala

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Sala já utilizada no mesmo dia e horário |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Selecionar uma sala já ocupada no mesmo horário.
2. Tentar salvar o quadro de horários.

**Resultado esperado:** Sistema impede o cadastro e informa conflito de utilização da sala.

---

## CT-058 — Cadastro de horário no primeiro horário disponível (limite)

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Horário inicial configurado para início das aulas |
| **Técnica** | BVA — limite inferior válido |
| **Prioridade** | Média |

**Passos:**

1. Cadastrar uma aula exatamente no primeiro horário permitido.

**Resultado esperado:** Cadastro realizado normalmente.

---

## CT-059 — Cadastro de horário antes do horário permitido

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Horário mínimo configurado para início das aulas |
| **Técnica** | BVA — abaixo do limite (inválido) |
| **Prioridade** | Média |

**Passos:**

1. Tentar cadastrar uma aula antes do horário inicial permitido.

**Resultado esperado:** Sistema bloqueia o cadastro informando horário inválido.

---

## CT-060 — Cadastro de horário no último horário permitido

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Horário final configurado para término das aulas |
| **Técnica** | BVA — limite superior válido |
| **Prioridade** | Média |

**Passos:**

1. Cadastrar uma aula exatamente no último horário permitido.

**Resultado esperado:** Cadastro realizado com sucesso.

---

## CT-061 — Cadastro de horário após o último horário permitido

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Horário final configurado para término das aulas |
| **Técnica** | BVA — acima do limite (inválido) |
| **Prioridade** | Média |

**Passos:**

1. Tentar cadastrar uma aula após o horário máximo permitido.

**Resultado esperado:** Sistema impede o cadastro informando que o horário está fora do período permitido.
