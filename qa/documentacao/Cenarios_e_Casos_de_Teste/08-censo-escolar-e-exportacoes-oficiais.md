# 8.0 Censo Escolar (Educacenso) e Exportações Oficiais

**Módulo:** Censo Escolar (Educacenso) e Exportações Oficiais **Aplicação:** i-Educar (Módulo Web) **Tipo de teste:** Manual funcional **Ambiente:** Homologação **Base:** regras-de-negocio.md **Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-048 — Exportação para o Educacenso com todos os cadastros consistentes

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Alunos, turmas e servidores com todos os dados obrigatórios preenchidos |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar a funcionalidade de exportação para o Educacenso.
2. Executar a validação dos dados.
3. Confirmar a geração do arquivo de exportação.

**Resultado esperado:** Validação concluída sem pendências e arquivo gerado com sucesso.

---

## CT-049 — Exportação com aluno possuindo cadastro incompleto

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Existe pelo menos um aluno com campo obrigatório não preenchido |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Executar a validação antes da exportação.
2. Consultar as pendências encontradas.

**Resultado esperado:** Sistema identifica o aluno com cadastro incompleto, impede sua exportação e exibe a pendência correspondente.

---

## CT-050 — Exportação com turma possuindo cadastro incompleto

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Existe pelo menos uma turma com dados obrigatórios incompletos |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Executar a validação para exportação.
2. Consultar o resultado da validação.

**Resultado esperado:** Sistema identifica a turma inconsistente, impedindo sua exportação até a correção dos dados.

---

## CT-051 — Exportação com servidor possuindo cadastro incompleto

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Existe pelo menos um servidor com cadastro incompleto |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Executar a validação antes da exportação.
2. Consultar as pendências apresentadas.

**Resultado esperado:** Sistema informa o cadastro incompleto do servidor e impede sua inclusão na exportação.

---

## CT-052 — Consulta do resultado da validação

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Validação executada previamente |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Executar uma validação.
2. Acessar a consulta dos resultados.

**Resultado esperado:** Sistema apresenta todas as pendências encontradas, indicando claramente os registros afetados e o motivo da inconsistência.

---

## CT-053 — Consulta do resultado de importação do Educacenso

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Existe uma importação do Educacenso previamente executada |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Acessar a consulta de importações do Educacenso.
2. Selecionar uma importação realizada.

**Resultado esperado:** Sistema apresenta o resultado da importação, indicando sucesso ou inconsistências encontradas.

---

## CT-054 — Geração da exportação após correção das pendências

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Todas as pendências identificadas na validação foram corrigidas |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Corrigir todos os cadastros apontados na validação.
2. Executar novamente a validação.
3. Gerar a exportação.

**Resultado esperado:** Sistema não apresenta pendências e gera o arquivo no formato exigido pelo SEB/Educacenso.
