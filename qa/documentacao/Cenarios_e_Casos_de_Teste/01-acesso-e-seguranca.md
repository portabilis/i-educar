# 1.0 Acesso e Segurança
 
**Módulo:** Acesso e Segurança
**Aplicação:** i-Educar (Módulo Web)
**Tipo de teste:** Manual funcional
**Ambiente:** Homologação
**Base:** [regras-de-negocio.md](../regras-de-negocio.md)
**Técnicas aplicadas:** Particionamento de Equivalência (PE)
 
> ⚠️ Todos os dados utilizados nestes testes (nomes, e-mails, CPFs, datas) são
> fictícios, criados especificamente para fins de teste.
 
---
 
## CT-001 — Login com credenciais válidas
 
| Campo | Detalhe |
|---|---|
| **Pré-condição** | Usuário cadastrado, com perfil ativo, vinculado a instituição regular (não suspensa) |
| **Técnica** | Particionamento de Equivalência — classe válida |
| **Prioridade** | Alta |
 
**Passos:**
1. Acessar a tela de login
2. Informar e-mail/usuário e senha corretos
3. Clicar em "Entrar"
**Resultado esperado:** Login efetuado com sucesso; sistema exibe os menus
correspondentes ao perfil do usuário.
 
---
 
## CT-002 — Login com senha incorreta
 
| Campo | Detalhe |
|---|---|
| **Pré-condição** | Usuário cadastrado e ativo |
| **Técnica** | Particionamento de Equivalência — classe inválida (credencial incorreta) |
| **Prioridade** | Alta |
 
**Passos:**
1. Acessar a tela de login
2. Informar usuário correto e senha incorreta
3. Clicar em "Entrar"
**Resultado esperado:** Sistema bloqueia o acesso e exibe mensagem de erro
genérica, sem indicar se o erro foi no usuário ou na senha.
 
---
 
## CT-003 — Login via provedor externo (SSO)
 
| Campo | Detalhe |
|---|---|
| **Pré-condição** | Instituição com SSO configurado e usuário elegível |
| **Técnica** | Particionamento de Equivalência — classe válida (fluxo alternativo) |
| **Prioridade** | Média |
 
**Passos:**
1. Acessar a tela de login
2. Selecionar a opção de login via provedor externo
3. Autenticar-se no provedor
4. Retornar ao i-Educar
**Resultado esperado:** Usuário é autenticado e redirecionado à área logada,
com o perfil correspondente aplicado.
 
---
 
## CT-004 — Login de usuário vinculado a instituição suspensa
 
| Campo | Detalhe |
|---|---|
| **Pré-condição** | Instituição com acesso suspenso (ex.: inadimplência) e usuário com credenciais válidas |
| **Técnica** | Particionamento de Equivalência — classe inválida (regra de negócio) |
| **Prioridade** | Alta |
 
**Passos:**
1. Acessar a tela de login
2. Informar credenciais válidas de um usuário vinculado à instituição suspensa
3. Clicar em "Entrar"
**Resultado esperado:** Sistema impede a operação, mesmo com credenciais
corretas, e informa que o acesso está indisponível para a instituição.
 
---
 
## CT-005 — Troca de senha obrigatória (senha temporária/expirada)
 
| Campo | Detalhe |
|---|---|
| **Pré-condição** | Usuário com senha marcada como temporária ou expirada |
| **Técnica** | Particionamento de Equivalência — classe válida (fluxo forçado) |
| **Prioridade** | Alta |
 
**Passos:**
1. Realizar login com a senha temporária/expirada
2. Tentar navegar diretamente para qualquer outra tela via URL
**Resultado esperado:** Sistema redireciona obrigatoriamente para a tela de
troca de senha e impede a navegação para qualquer outra área até a senha ser
alterada.
 
---
 
## CT-006 — Exibição de menus por perfil (visualizar x modificar)
 
| Campo | Detalhe |
|---|---|
| **Pré-condição** | Dois usuários de teste: um com permissão apenas de visualização e outro com permissão de visualização + modificação, no mesmo módulo |
| **Técnica** | Particionamento de Equivalência — classes de perfil |
| **Prioridade** | Alta |
 
**Passos:**
1. Logar com o usuário que só tem permissão de visualização
2. Verificar se botões de edição/exclusão estão ocultos ou desabilitados
3. Repetir o login com o usuário que tem permissão de modificação
4. Verificar se os botões de edição/exclusão aparecem habilitados
**Resultado esperado:** Cada perfil exibe estritamente as ações permitidas;
usuário somente-leitura não consegue modificar dados, nem forçando a ação
via requisição direta.
 
---
 
## CT-007 — Isolamento de dados entre instituições
 
| Campo | Detalhe |
|---|---|
| **Pré-condição** | Dois usuários de teste vinculados a instituições diferentes |
| **Técnica** | Particionamento de Equivalência — classe inválida (acesso cruzado) |
| **Prioridade** | Alta |
 
**Passos:**
1. Logar como usuário da Instituição A
2. Tentar acessar um registro (aluno, turma, etc.) pertencente à Instituição B,
   inclusive manipulando o ID diretamente na URL
3. Repetir o teste no sentido inverso (Instituição B tentando acessar dados da A)
**Resultado esperado:** Sistema nega o acesso em ambos os sentidos,
independentemente da tentativa de acesso direto por URL/ID.
