# 10.0 Servidores

**Módulo:** Servidores **Aplicação:** i-Educar (Módulo Web) **Tipo de teste:** Manual funcional **Ambiente:** Homologação **Base:** regras-de-negocio.md **Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-062 — Cadastro de servidor com dados obrigatórios preenchidos

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Usuário com permissão para cadastrar servidores |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar o cadastro de servidores.
2. Informar todos os campos obrigatórios.
3. Salvar o cadastro.

**Resultado esperado:** Servidor cadastrado com sucesso.

---

## CT-063 — Cadastro de servidor sem preencher campo obrigatório

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Usuário com permissão para cadastrar servidores |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar o cadastro de servidores.
2. Deixar um campo obrigatório em branco.
3. Tentar salvar.

**Resultado esperado:** Sistema impede o cadastro e destaca os campos obrigatórios não preenchidos.

---

## CT-064 — Cadastro de servidor com CPF válido

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | CPF inexistente na base e válido |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Informar um CPF válido.
2. Preencher os demais campos obrigatórios.
3. Salvar.

**Resultado esperado:** Cadastro realizado com sucesso.

---

## CT-065 — Cadastro de servidor com CPF já cadastrado

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | CPF já existente no sistema |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Informar um CPF já cadastrado.
2. Tentar salvar o cadastro.

**Resultado esperado:** Sistema impede o cadastro e informa que o CPF já está em uso.

---

## CT-066 — Cadastro com data de admissão no limite mínimo permitido

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Sistema configurado para aceitar a data mínima definida |
| **Técnica** | BVA — limite inferior válido |
| **Prioridade** | Média |

**Passos:**

1. Informar a menor data de admissão permitida.
2. Salvar o cadastro.

**Resultado esperado:** Cadastro realizado normalmente.

---

## CT-067 — Cadastro com data de admissão anterior ao limite permitido

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Existe uma data mínima configurada para admissão |
| **Técnica** | BVA — abaixo do limite (inválido) |
| **Prioridade** | Média |

**Passos:**

1. Informar uma data anterior ao limite permitido.
2. Tentar salvar.

**Resultado esperado:** Sistema bloqueia o cadastro informando data de admissão inválida.

---

## CT-068 — Inativação de servidor

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Servidor cadastrado e ativo |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Localizar um servidor ativo.
2. Selecionar a opção de inativação.
3. Confirmar a operação.

**Resultado esperado:** Servidor passa para a situação de inativo e deixa de ser considerado nas operações que exigem servidor ativo.
