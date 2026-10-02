# 7.0 Frequência e Situação Final

**Módulo:** Frequência e Situação Final **Aplicação:** i-Educar (Módulo Web) **Tipo de teste:** Manual funcional **Ambiente:** Homologação **Base:** regras-de-negocio.md **Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-041 — Registro de falta para aluno

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Aluno matriculado em turma e componente curricular |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar o lançamento de frequência.
2. Selecionar uma turma e componente curricular.
3. Registrar uma falta para um aluno.
4. Salvar.

**Resultado esperado:** Frequência registrada com sucesso e disponível para consulta.

---

## CT-042 — Importação válida da situação final

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Arquivo de importação preenchido conforme o layout esperado |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar a funcionalidade de importação da situação final.
2. Selecionar um arquivo válido.
3. Executar a análise do arquivo.
4. Realizar o mapeamento das colunas.
5. Confirmar a importação.

**Resultado esperado:** Importação iniciada com sucesso e processamento registrado.

---

## CT-043 — Importação com arquivo inválido

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Arquivo fora do layout esperado ou corrompido |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar a importação da situação final.
2. Selecionar um arquivo inválido.
3. Executar a análise.

**Resultado esperado:** Sistema rejeita o arquivo e informa o motivo da inconsistência.

---

## CT-044 — Confirmação da importação sem realizar o mapeamento

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Arquivo analisado, porém sem mapeamento das colunas |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Média |

**Passos:**

1. Importar um arquivo válido.
2. Ignorar a etapa de mapeamento.
3. Tentar confirmar a importação.

**Resultado esperado:** Sistema impede a confirmação e solicita o mapeamento das colunas.

---

## CT-045 — Consulta de processamento em andamento

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Importação iniciada e processamento ainda não concluído |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Iniciar uma importação.
2. Acessar a tela de acompanhamento do processamento.

**Resultado esperado:** Sistema exibe o status **"Em andamento"**.

---

## CT-046 — Consulta de processamento concluído

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Processamento finalizado com sucesso |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Concluir uma importação.
2. Consultar o status do processamento.

**Resultado esperado:** Sistema exibe o status **"Concluído"** e disponibiliza o resultado da operação.

---

## CT-047 — Execução de alteração em lote após simulação

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Dados selecionados para alteração em lote |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Selecionar registros para alteração em lote.
2. Executar a simulação/prévia.
3. Conferir os resultados apresentados.
4. Confirmar a execução definitiva.

**Resultado esperado:** Alteração em lote executada conforme a prévia apresentada, iniciando o processamento assíncrono.
