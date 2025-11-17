# Solução para Issue #1060 - Validação de CEP

## Problema Identificado

Durante a execução de testes na funcionalidade de Cadastro de Pessoa Física (Menu: **Cadastros > Pessoas > Pessoas físicas > Novo**), foi identificado que ao preencher o formulário com um CEP inválido/inexistente, o sistema não exibia nenhuma mensagem de erro ou validação.

### Comportamento Anterior:
- ❌ CEPs obviamente inválidos (00000000, 123, 99999999) eram aceitos sem validação
- ❌ Nenhuma mensagem de erro era exibida quando o CEP não era encontrado
- ❌ Usuário não recebia feedback sobre problemas com o CEP
- ❌ Possibilidade de cadastrar pessoas com endereços inconsistentes

## Solução Implementada

### Arquivo Modificado:
- `public/vendor/legacy/Cadastro/Assets/Javascripts/Addresses.js`

### Alterações Realizadas:

1. **Validação de Formato de CEP**
   - Verifica se o CEP está no formato correto (00000-000)
   - Exibe mensagem: "Formato de CEP inválido. Use o formato: 00000-000"

2. **Validação de CEPs Obviamente Inválidos**
   - Detecta CEPs como 00000000, 99999999 ou sequências repetidas (11111111, etc)
   - Exibe mensagem: "CEP inválido. Por favor, verifique o número digitado."

3. **Tratamento de CEP Não Encontrado (404)**
   - Captura erro 404 quando a API não encontra o CEP
   - Exibe mensagem: "CEP não encontrado. Verifique se o número está correto ou preencha o endereço manualmente."

4. **Tratamento de Outros Erros**
   - Captura outros erros da API
   - Exibe mensagem: "Erro ao buscar CEP. Tente novamente ou preencha o endereço manualmente."

5. **Limpeza Automática de Mensagens**
   - Remove mensagens de erro automaticamente quando o usuário limpa o campo
   - Melhora a experiência do usuário

### Comportamento Atual:
- ✅ Valida formato do CEP antes de fazer a busca
- ✅ Detecta CEPs inválidos (00000000, 99999999, etc)
- ✅ Exibe mensagem clara quando CEP não é encontrado
- ✅ Exibe mensagem clara quando há erro de formato
- ✅ Remove mensagens de erro automaticamente ao corrigir
- ✅ Previne cadastros com endereços inconsistentes

## Como Testar

### Teste 1: CEP Inválido (formato incorreto)
1. Acesse: **Cadastros > Pessoas > Pessoas físicas > Novo**
2. Digite no campo CEP: `123`
3. Clique em "Preencher automaticamente usando o CEP"
4. **Resultado esperado**: Mensagem "Formato de CEP inválido. Use o formato: 00000-000"

### Teste 2: CEP Obviamente Inválido
1. Acesse: **Cadastros > Pessoas > Pessoas físicas > Novo**
2. Digite no campo CEP: `00000-000`
3. Clique em "Preencher automaticamente usando o CEP"
4. **Resultado esperado**: Mensagem "CEP inválido. Por favor, verifique o número digitado."

### Teste 3: CEP Inexistente
1. Acesse: **Cadastros > Pessoas > Pessoas físicas > Novo**
2. Digite no campo CEP: `12345-678` (CEP que não existe)
3. Clique em "Preencher automaticamente usando o CEP"
4. **Resultado esperado**: Mensagem "CEP não encontrado. Verifique se o número está correto ou preencha o endereço manualmente."

### Teste 4: CEP Válido
1. Acesse: **Cadastros > Pessoas > Pessoas físicas > Novo**
2. Digite no campo CEP: `88015-900` (CEP válido de Florianópolis)
3. Clique em "Preencher automaticamente usando o CEP"
4. **Resultado esperado**: Endereço preenchido automaticamente sem erros

## Como Abrir o Pull Request

### Passo 1: Verificar Branch
```bash
git branch
# Deve estar em: fix/cep-validation-message-1060
```

### Passo 2: Fazer Push da Branch
```bash
git push origin fix/cep-validation-message-1060
```

### Passo 3: Abrir Pull Request no GitHub
1. Acesse: https://github.com/portabilis/i-educar
2. Clique em "Compare & pull request" (aparecerá automaticamente após o push)
3. Ou vá em "Pull requests" > "New pull request"
4. Selecione:
   - **base**: `2.10` (ou a branch principal do projeto)
   - **compare**: `fix/cep-validation-message-1060`

### Passo 4: Preencher Informações do PR

**Título:**
```
fix: adicionar validação e mensagem de erro para CEP inválido/inexistente (#1060)
```

**Descrição:**
```markdown
## Descrição
Implementa validação de CEP com mensagens de erro claras para o usuário durante o cadastro de pessoa física.

## Issue Relacionada
Fixes #1060

## Tipo de Mudança
- [x] Bug fix (correção de bug que não quebra funcionalidades existentes)
- [ ] Nova funcionalidade
- [ ] Breaking change
- [ ] Melhoria de performance
- [x] Melhoria de UX/UI

## O que foi alterado?
- Adicionada validação de formato de CEP (00000-000)
- Adicionada validação para CEPs obviamente inválidos (00000000, 99999999, etc)
- Adicionado tratamento de erro 404 quando CEP não é encontrado
- Adicionadas mensagens de erro claras e descritivas para o usuário
- Implementada limpeza automática de mensagens ao corrigir o campo

## Arquivo Modificado
- `public/vendor/legacy/Cadastro/Assets/Javascripts/Addresses.js`

## Como Testar
1. Acesse: **Cadastros > Pessoas > Pessoas físicas > Novo**
2. Teste com CEP inválido: `00000-000`
   - Esperado: Mensagem de erro clara
3. Teste com CEP inexistente: `12345-678`
   - Esperado: Mensagem "CEP não encontrado"
4. Teste com CEP válido: `88015-900`
   - Esperado: Endereço preenchido automaticamente

## Screenshots
(Adicione screenshots mostrando as mensagens de erro)

## Checklist
- [x] Código segue o padrão do projeto
- [x] Comentários foram adicionados onde necessário
- [x] Documentação foi atualizada
- [x] Mudanças não quebram funcionalidades existentes
- [x] Testado localmente
```

### Passo 5: Adicionar Labels (se tiver permissão)
- `bug`
- `enhancement`
- `UX/UI`

### Passo 6: Solicitar Revisores
- Adicione revisores da equipe Portábilis ou maintainers do projeto

### Passo 7: Aguardar Revisão
- Responda aos comentários dos revisores
- Faça ajustes se solicitado
- Aguarde aprovação e merge

## Próximos Passos (Opcional)

### Melhorias Futuras Sugeridas:
1. Adicionar validação de CEP no backend (controller/request)
2. Adicionar testes automatizados (JavaScript unit tests)
3. Melhorar estilização das mensagens de erro
4. Adicionar debounce na validação ao digitar
5. Adicionar analytics para rastrear CEPs inválidos mais comuns

## Informações Técnicas

### Tecnologias Utilizadas:
- JavaScript (jQuery)
- API REST (`/api/postal-code/{postalCode}`)
- OpenCEP API (via backend)

### Navegadores Testados:
- Chrome 141.0.7390.66
- (Adicionar outros navegadores testados)

### Ambiente de Teste:
- Plataforma: Docker
- SO: Windows 10
- Navegador: Chrome 141.0.7390.66

---

**Desenvolvido por:** Murilo Martins  
**Data:** 2025-01-17  
**Issue:** #1060

