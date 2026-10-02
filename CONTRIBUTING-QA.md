# Guia de contribuição para QA

Este documento complementa o [Guia de contribuição](./CONTRIBUTING.md) geral do
i-Educar e é voltado especificamente para quem quer contribuir com **qualidade
de software**: testes manuais, testes exploratórios, automação e reporte
estruturado de bugs. Se você não escreve código mas testa sistemas, este é o
seu lugar.

## Por que um processo de QA dedicado

O i-Educar é um sistema grande, usado por secretarias de educação, escolas,
gestores e professores em produção. Bugs aqui não são só um incômodo técnico:
podem impactar o registro de notas, frequência, matrícula e outros processos
que afetam pessoas reais. Ter um processo de QA claro ajuda a comunidade a:

- Encontrar problemas antes que cheguem à produção;
- Reportar bugs de um jeito que qualquer mantenedor consiga reproduzir e
  priorizar rapidamente;
- Separar o que é **bug** (comportamento quebrado) do que é **melhoria**
  (algo que funciona, mas poderia ser melhor);
- Criar um histórico de testes que sirva de referência para quem for mexer
  naquele módulo no futuro.

## Como contribuir com QA

- [Testando módulos existentes](#testando-módulos-existentes)
- [Escrevendo casos de teste](#escrevendo-casos-de-teste)
- [Reportando bugs encontrados em testes](#reportando-bugs-encontrados-em-testes)
- [Registrando sugestões de melhoria](#registrando-sugestões-de-melhoria)
- [Testes exploratórios](#testes-exploratórios)
- [Automação de testes](#automação-de-testes)
- [Ambiente de testes](#ambiente-de-testes)

### Testando módulos existentes

Antes de sair testando por conta própria, verifique:

1. Se já existe alguma issue com o label **qa** ou **testes** falando sobre o
   módulo que você quer cobrir;
2. Se há um plano de testes em andamento nas discussões da issue relacionada;
3. Se o módulo já tem casos de teste documentados (verifique a pasta `/qa` do
   repositório, quando existir, ou pergunte na issue).

Escolhido o módulo, abra uma issue com o label **qa** avisando que você vai
testá-lo, para evitar que duas pessoas testem a mesma coisa em paralelo.

### Escrevendo casos de teste

Os casos de teste devem seguir formato tradicional (passo a passo), não
Gherkin, para manter a leitura simples tanto para quem tem quanto para quem
não tem experiência técnica. Use a nomenclatura `CT-NNN` (numeração sequencial
por módulo) e o seguinte formato:

```
**CT-001 — [Nome do módulo] — [Ação testada]**

**Pré-condição:** O que precisa estar configurado ou logado antes do teste.

**Passos:**
1. Passo 1
2. Passo 2
3. Passo 3

**Resultado esperado:** O que deveria acontecer.

**Resultado obtido:** O que de fato aconteceu.

**Status:** Passou / Falhou / Bloqueado
```

Exemplo:

```
**CT-014 — Escola > Cadastro > Turma — Cadastrar turma multisseriada**

**Pré-condição:** Usuário logado com perfil de secretário, escola e curso
já cadastrados.

**Passos:**
1. Acessar Escola > Cadastro > Turma > Cadastrar
2. Marcar a opção "Multisseriada"
3. Verificar as séries disponíveis para seleção

**Resultado esperado:** Devem aparecer apenas as séries vinculadas à escola
e ao curso selecionado.

**Resultado obtido:** Todas as séries do sistema aparecem, independente da
escola/curso.

**Status:** Falhou
```

Casos de teste que resultarem em falha devem originar uma issue de bug
separada (veja a seção abaixo), referenciando o `CT-NNN` correspondente.

### Reportando bugs encontrados em testes

Bugs encontrados durante testes seguem o
[processo de reporte de bugs](./CONTRIBUTING.md#reportando-bugs) do guia
geral, com um complemento: use o identificador `BUG-AAAAMMDD-NNN` (data do
dia do reporte + número sequencial) no título da issue, e referencie o
`CT-NNN` que originou o bug, quando aplicável.

```
**MENU:** Caminho do menu no sistema.

**DESCRIÇÃO:** Descrição do problema encontrado.

**CASO DE TESTE RELACIONADO:** CT-NNN (se aplicável)

**PASSOS PARA REPRODUZIR:**
1. Passo 1
2. Passo 2

**RESULTADO ESPERADO:** O que deveria acontecer.

**RESULTADO OBTIDO:** O que aconteceu de fato.

**AMBIENTE:** Navegador, versão, sistema operacional.

**OBSERVAÇÕES:** Qualquer contexto adicional.
```

Adicione os labels **bug** e **qa** à issue.

### Registrando sugestões de melhoria

Nem tudo que chama atenção durante o teste é um bug — muitas vezes é uma
oportunidade de melhorar a experiência sem que nada esteja "quebrado" (ex:
um fluxo confuso, uma mensagem de erro pouco clara, um campo sem máscara de
formatação). Nesses casos, use o identificador `MELHORIA-AAAAMMDD-NNN` e siga
o processo de [indicação de melhorias](./CONTRIBUTING.md#indicando-melhorias)
do guia geral, com o label **melhoria** + **qa**.

Manter bugs e melhorias em categorias separadas ajuda os mantenedores a
priorizar corretamente: um bug tende a ter urgência maior que uma melhoria de
UX.

### Testes exploratórios

Além dos casos de teste escritos, sessões de teste exploratório são bem-vindas
— principalmente em módulos novos ou pouco cobertos. Ao final de uma sessão,
registre um resumo na issue correspondente com:

- Módulo e tempo dedicado à sessão;
- O que foi explorado (fluxos, combinações de dados, casos de borda);
- Bugs e melhorias encontrados (linkando as issues criadas);
- Áreas que ficaram sem cobertura e podem precisar de atenção futura.

### Automação de testes

Se você quiser contribuir com testes automatizados (end-to-end, API ou
integração), abra antes uma issue com o label **qa** descrevendo o escopo e a
ferramenta proposta, para alinhar com o que já existe ou está em andamento no
projeto. Ao abrir o PR de automação, siga também os requisitos de
[pull requests](./CONTRIBUTING.md#fazendo-pull-requests) do guia geral,
incluindo:

- Testes devem ser independentes entre si (não depender de ordem de execução
  nem de estado deixado por outro teste);
- Dados de teste devem ser criados e limpos pelo próprio teste sempre que
  possível, evitando dependência de dados fixos em produção;
- Inclua instruções de como rodar a suíte localmente no próprio PR.

### Ambiente de testes

Para testar o i-Educar localmente, siga as instruções de instalação do
[README](./README.md). Se encontrar problemas específicos do ambiente de
testes (setup, massa de dados, containers), reporte como bug normalmente,
usando o label **qa** além de **bug**, para que fique claro que o problema
afeta quem quer contribuir com testes.

---

Dúvidas sobre este processo? Abra uma discussão com o label **qa** ou escreva
para `comunidade@portabilis.com.br`.
