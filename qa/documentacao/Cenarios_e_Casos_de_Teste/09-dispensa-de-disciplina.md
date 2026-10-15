# 9.0 Dispensa de Disciplina

**Módulo:** Dispensa de Disciplina **Aplicação:** i-Educar (Módulo Web) **Tipo de teste:** Manual funcional **Ambiente:** Homologação **Base:** regras-de-negocio.md **Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-055 — Dispensa individual de disciplina com justificativa

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Aluno matriculado na disciplina e período letivo ativo |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar a funcionalidade de dispensa de disciplina.
2. Selecionar um aluno e a disciplina.
3. Informar uma justificativa.
4. Confirmar a operação.

**Resultado esperado:** Dispensa registrada com sucesso e vinculada à justificativa informada.

---

## CT-056 — Tentativa de dispensa individual sem justificativa

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Aluno matriculado na disciplina |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Selecionar um aluno e uma disciplina.
2. Deixar o campo de justificativa em branco.
3. Confirmar a operação.

**Resultado esperado:** Sistema impede a concessão da dispensa e informa que a justificativa é obrigatória.

---

## CT-057 — Tentativa de conceder dispensa duplicada

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Já existe uma dispensa cadastrada para o mesmo aluno, disciplina e período |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Localizar um aluno que já possua dispensa registrada.
2. Tentar conceder uma nova dispensa para a mesma disciplina e período.

**Resultado esperado:** Sistema bloqueia a operação informando que já existe uma dispensa cadastrada.

---

## CT-058 — Dispensa em lote de alunos com justificativa

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Conjunto de alunos matriculados na disciplina |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Selecionar vários alunos.
2. Escolher a disciplina.
3. Informar uma justificativa.
4. Confirmar a operação.

**Resultado esperado:** Dispensa registrada para todos os alunos selecionados.

---

## CT-059 — Dispensa em lote contendo aluno já dispensado

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Pelo menos um dos alunos já possui dispensa para a disciplina e período |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Média |

**Passos:**

1. Selecionar um grupo de alunos, incluindo um já dispensado.
2. Informar a justificativa.
3. Confirmar a operação.

**Resultado esperado:** Sistema identifica a duplicidade para o aluno já dispensado, impedindo o cadastro duplicado e informando a ocorrência.

---

## CT-060 — Consulta da lista de dispensas concedidas

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Existem dispensas previamente cadastradas |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Acessar a consulta de dispensas.
2. Pesquisar por aluno, disciplina ou período.

**Resultado esperado:** Sistema apresenta a lista de dispensas concedidas conforme os filtros informados.

---

## CT-061 — Consulta de dispensa inexistente

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Não existe dispensa para os filtros informados |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Baixa |

**Passos:**

1. Acessar a consulta de dispensas.
2. Informar filtros que não possuam registros.

**Resultado esperado:** Sistema informa que não foram encontradas dispensas para os critérios pesquisados.
