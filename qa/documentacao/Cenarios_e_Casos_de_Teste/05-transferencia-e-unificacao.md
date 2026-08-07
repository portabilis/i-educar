# 5.0 Transferência e Unificação de Cadastros

**Módulo:** Transferência e Unificação de Cadastros
**Aplicação:** i-Educar (Módulo Web)
**Tipo de teste:** Manual funcional
**Ambiente:** Homologação
**Base:** [regras-de-negocio.md](../regras-de-negocio.md)
**Técnicas aplicadas:** Particionamento de Equivalência (PE)

> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são
> fictícios, criados especificamente para fins de teste.

---

## CT-029 — Confirmação de transferência com token válido

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Transferência de aluno solicitada, token de segurança gerado e não expirado |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**
1. Acessar a confirmação de transferência com o token válido
2. Confirmar a transferência

**Resultado esperado:** Transferência processada com sucesso.

---

## CT-030 — Confirmação de transferência com token expirado

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Token de segurança gerado e já expirado |
| **Técnica** | Particionamento de Equivalência — classe inválida |
| **Prioridade** | Alta |

**Passos:**
1. Acessar a confirmação de transferência utilizando um token expirado
2. Tentar confirmar

**Resultado esperado:** Sistema rejeita a confirmação e informa que o token
é inválido/expirado, sem processar a transferência.

---

## CT-031 — Unificação de cadastros duplicados

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Dois cadastros de uma mesma pessoa, identificados como duplicados |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**
1. Selecionar os dois cadastros duplicados
2. Executar a unificação, indicando qual registro será mantido

**Resultado esperado:** Cadastros unificados em um único registro; um log da
operação é gerado, indicando qual registro foi absorvido por qual.

---

## CT-032 — Desfazer unificação já realizada

| Campo | Detalhe |
|---|---|
| **Pré-condição** | Existe uma unificação de cadastros já realizada (ver CT-031) |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |

**Passos:**
1. Acessar o histórico/log de unificações
2. Selecionar a unificação realizada no CT-031
3. Executar a reversão

**Resultado esperado:** Os cadastros voltam a existir separadamente, com os
dados originais preservados, e o histórico mantém o rastro de que houve
unificação e posterior reversão.
