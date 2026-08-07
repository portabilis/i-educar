# 11.0 Cadastro de Usuários

**Módulo:** Cadastro de Usuários **Aplicação:** i-Educar (Módulo Web) **Tipo de teste:** Manual funcional **Ambiente:** Homologação **Base:** regras-de-negocio.md **Técnicas aplicadas:** Particionamento de Equivalência (PE) e Análise de Valor Limite (BVA)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são fictícios, criados especificamente para fins de teste.

---

## CT-069 — Cadastro de usuário com dados válidos

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Usuário logado com permissão para gerenciar usuários |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar o cadastro de usuários.
2. Informar todos os campos obrigatórios.
3. Definir um perfil de acesso.
4. Salvar.

**Resultado esperado:** Usuário cadastrado com sucesso.

---

## CT-070 — Cadastro de usuário sem preencher campo obrigatório

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Usuário logado com permissão para gerenciar usuários |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Acessar o cadastro de usuários.
2. Deixar um campo obrigatório sem preenchimento.
3. Tentar salvar.

**Resultado esperado:** Sistema impede o cadastro e informa os campos obrigatórios.

---

## CT-071 — Cadastro de usuário com login já existente

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Existe um usuário cadastrado com o mesmo login |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**

1. Informar um login já utilizado.
2. Preencher os demais campos obrigatórios.
3. Salvar.

**Resultado esperado:** Sistema bloqueia o cadastro informando que o login já existe.

---

## CT-072 — Cadastro de usuário com senha no tamanho mínimo permitido

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Sistema configurado com tamanho mínimo de senha |
| **Técnica** | BVA — limite inferior válido |
| **Prioridade** | Média |

**Passos:**

1. Informar uma senha exatamente com a quantidade mínima de caracteres permitida.
2. Salvar o cadastro.

**Resultado esperado:** Cadastro realizado com sucesso.

---

## CT-073 — Cadastro de usuário com senha abaixo do tamanho mínimo

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Sistema configurado com tamanho mínimo de senha |
| **Técnica** | BVA — abaixo do limite (inválido) |
| **Prioridade** | Média |

**Passos:**

1. Informar uma senha com quantidade inferior ao mínimo permitido.
2. Tentar salvar.

**Resultado esperado:** Sistema impede o cadastro e informa que a senha não atende aos requisitos mínimos.

---

## CT-074 — Alteração de perfil de acesso de usuário

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Usuário cadastrado e ativo |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Localizar um usuário ativo.
2. Alterar o perfil de acesso.
3. Salvar.

**Resultado esperado:** Perfil atualizado com sucesso e novas permissões aplicadas.

---

## CT-075 — Inativação de usuário

| **CampoDetalhe** | |
| ---------------- | -------------------------------------------- |
| **Pré-condição** | Usuário cadastrado e ativo |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Média |

**Passos:**

1. Localizar um usuário ativo.
2. Selecionar a opção de inativação.
3. Confirmar a operação.

**Resultado esperado:** Usuário passa para a situação de inativo e deixa de acessar o sistema.
