# Guia de contribuição

Que bom que você resolveu contribuir conosco. Obrigado! A proposta do i-Educar é
ambiciosa e precisamos de toda ajuda possível para alcançar nossos objetivos.
Neste guia vamos explicar como funcionam os nossos processos internos e como
podemos trabalhar juntos da melhor forma possível.

## Como contribuir

Existem diversas formas de contribuir com o projeto:

- [Reportando bugs](#reportando-bugs)
- [Indicando melhorias](#indicando-melhorias)
- [Pedindo recursos](#pedindo-recursos)
- [Discutindo as issues](#discutindo-as-issues)
- [Fazendo pull requests](#fazendo-pull-requests)
- [Outras formas de contribuir](#outras-formas-de-contribuir)

### Reportando bugs

Se encontrou um bug no i-Educar você pode reportá-lo usando a ferramenta de
[issues do GitHub](https://github.com/portabilis/i-educar/issues). Porém antes
de enviar seu bug é importante fazer as seguintes verificações:

1. Atualize seu repositório local no branch `master` mais recente. Talvez seu
   bug já tenha sido corrigido na versão mais recente;
2. Verifique se o bug já foi reportado por outra pessoa fazendo uma busca pelas
   issues.

Se o bug realmente não foi resolvido ou acolhido então está na hora de
[criar uma nova issue](https://github.com/portabilis/i-educar/issues/new). No
título da issue tente resumir da melhor forma o problema evitando títulos
genéricos como *"Falha no sistema"* ou *"Problemas na instalação"*. No corpo da
issue, tente seguir o seguinte formato:

```
**MENU:** Nesta seção deve ser apontado qual o menu no sistema deve ser
realizada a alteração.

**DESCRIÇÃO:** Nesta seção deve ser colocado uma descrição do problema ou
necessidade.

**OBSERVAÇÕES:** Observações em geral sobre o problema apontado.
```

Exemplo:

```
**MENU:**

i-Educar - Escola > Cadastro > Turma > Cadastrar

**DESCRIÇÃO:**

No cadastro de turmas, ao selecionar a opção Multisseriada, todas as séries
aparecem para seleção, quando deveria ser somente as da escola e curso
selecionado.

**OBSERVAÇÕES:**

Não se aplica.

```

Se possível inclua imagens ou vídeos à descrição do bug para facilitar o
processo de reprodução. Use um software como
[LICEcap](https://www.cockos.com/licecap/) para criar um gif animado de sua
tela. Informe também detalhes sobre o seu ambiente: plataforma de execução,
sistema operacional, navegador e versão, etc. Você também deve adicionar o
label **bug** à issue.

#### Nota sobre falhas de segurança

Se você encontrou alguma falha de segurança **não use as issues para reportar o
bug**. Escreva o seu report diretamente para o endereço de e-mail
`comunidade@portabilis.com.br`. Ele será analisado, validado e corrigido de
acordo com as necessidades. Pedimos que **não torne a falha pública** para
segurança de todos que utilizam a plataforma em seu estado atual.

### Indicando melhorias

Outra ótima forma de contribuir é indicando melhorias ao código do i-Educar e em
como ele está estruturado. Se você tem qualquer ideia de como podemos melhorar
alguma abordagem na solução de problemas, refatoração de código, melhoria em
algum recurso ou qualquer outra coisa relacionada, siga estes passos:

1. Certifique-se de que sua ideia já não esteja sendo abordada em nosso
   [roadmap](./README.md#roadmap-de-tecnologia);
2. Também verifique se a ideia já não está pressente em nossas
   [issues do GitHub](https://github.com/portabilis/i-educar/issues);
3. Defenda a sua ideia e explique de forma convincente porque ela deve ser
   acolhida. Eis algumas questões a considerar:
   1. Você realmente esta propondo uma ideia só ou um conjunto de ideias?
   2. Qual é o problema que sua ideia resolve?
   3. Por que sua sugestão é melhor do que o que já existe no código?
   4. Realmente vale a pena demandar tempo para implementar sua ideia dentro de
      nossas prioridades?

Tendo passado pelo crivo de todos estes questionamentos basta
[criar uma nova issue](https://github.com/portabilis/i-educar/issues/new)
descrevendo as melhorias e usando o label **melhorias**.

### Pedindo recursos

Tendo em vista o que estamos construindo junto com a comunidade, novos recursos
tem baixa prioridade no nosso fluxo. Vale a pena enviar sua sugestão de recurso
apenas se:

1. O recurso em questão resolve um problema que não é resolvido por nada que já
   exista no projeto;
2. O recurso resolve um problema real validado por pessoas que estão em contato
   direto com a utilização da plataforma: secretários, gestores, professores,
   alunos, etc.

Um exemplo de um bom recurso implementado para o i-Educar é o módulo do **censo
escolar** que visa resolver todo o processo de exportação de dados para o censo
escolar com apenas alguns cliques, otimizando de forma radical este processo que
antes poderia demorar semanas envolvendo o trabalho de muitas pessoas.

Para criar seu pedido de recurso basta
[criar uma nova issue](https://github.com/portabilis/i-educar/issues/new) usando
o label **recurso**.

### Discutindo as issues

Antes de partirmos para o código em si é muito importante discutirmos com a
comunidade como cada issue será abordada. Toda e qualquer questão deve ser
colocada em discussão para que qualquer pessoa que deseje solucionar aquele
problema tenha o máximo de informações para executar uma solução.

Idealmente todas as issues devem ter um plano de ação claro antes que qualquer
código seja escrito. Sabemos que muitas vezes isto não é possível, sendo
necessário explorar e analisar melhor o que foi indicado. Nestes casos, publique
todas as suas descobertas nas discussões indicando caminhos e recebendo o
feedback da comunidade a respeito do que está sendo proposto.

Issues que estão em processo de discussão devem receber o label **discussão**
indicando que aquela issue precisa dos inputs e feedbacks dos membros da
comunidade. Nós te encorajamos a participar o máximo possível mas fique atento
ao nosso [código de conduta](./CODE_OF_CONDUCT.md) antes de realizar qualquer
interação com os outros membros da comunidade.

### Fazendo pull requests

Depois de ter um plano de ação relativamente claro você deve estar pronto para
contribuir com código. Para isso faça um fork do i-Educar e trabalhe em cima de
um branch diferente de master implementando suas soluções. Para saber mais sobre
pull requests e como eles funcionam, veja
[este link](https://help.github.com/articles/about-pull-requests/).

Antes de abrir seu PR (pull request) certique-se que:

- O código realmente resolve um problema real (de preferência baseado em alguma
  issue levantada);
- Seu PR resolve uma issue apenas. Se você quiser fazer mais de uma coisa,
  divida em vários PRs;
- Seu código é funcional (ou algo próximo disso). Providencie testes se
  possível;
- Seu código adere às convenções do [PSR-2](https://www.php-fig.org/psr/psr-2/);
- Seus commits englobam bem as funcionalidades desenvolvidas. Evite espalhar o
  desenvolvimento de um recurso em múltiplos commits;
- Se for inevitável criar vários commits intermediários, por favor execute um
  [squash](https://git-scm.com/book/pt-br/v1/Ferramentas-do-Git-Reescrevendo-o-Hist%C3%B3rico#Achatando-um-Commit)
  antes de abrir seu PR;
- As mensagens de seus commits são claras e descrevem bem o trabalho. Para
  maiores dicas sobre como escrever mensagens de commit adequadas, veja
  [este guia](https://chris.beams.io/posts/git-commit/).

Caso seu PR não atenda a uma destas demandas ele poderá ser fechado. Isto inclui
PRs que tentam resolver problemas reais mas que contém código cheio de erros ou
soluções incompletas. Para que a nossa lista de PRs não fique poluída,
dificultando o trabalho de outros membros da comunidade que podem ajudar
revisando as mudanças, pedimos que PRs sejam abertos quando sua solução estiver
a mais completa possível. Por isso é imprescindível usar a discussão nas issues
para a criação de soluções mais assertivas.

#### Sobre mudanças cosméticas

PRs que realizam apenas mudanças cosméticas como remoção de espaços em branco,
ajustes de indentação, etc., não serão aceitos. Nós valorizamos um código bem
escrito e queremos padronizar nossas práticas, mas PRs que não entregarem
nenhuma melhoria na estabilidade, funcionalidade, testabilidade do projeto ou
compatibilidade com os padrões sendo adotados (PSR-2) serão fechados. Para
entender melhor sobre esta decisão veja
[esta discussão](https://github.com/rails/rails/pull/13771#issuecomment-32746700).

### Outras formas de contribuir

Se você não trabalha com código mas quer ajudar o i-Educar, existe muitas outras
formas de contribuir:

- Ajude com a documentação do projeto (mais informações em breve);
- Fale sobre o i-Educar nas suas redes sociais, blogs, etc. Espalhe a palavra;
- Organize eventos e dê palestras sobre o i-Educar;
- Crie material promocional como apresentações, screencasts, mídia para
  compartilhamento em redes sociais, etc;
- Viu alguma discussão que te interessa e onde você pode acrescentar mesmo sem
  conhecimento técnico? Não se acanhe e participe também nas issues do GitHub.

Pensou em alguma outra forma de contribuir? Compartilha com a gente! Escreva
para `comunidade@portabilis.com.br` e conte sua história.

# Créditos

Este documento foi escrito com o auxílio de outros documentos similares
utilizados em outras comunidades. Destacamos:

- [Metabase](https://github.com/metabase/metabase/blob/master/docs/contributing.md)
- [Ghost](https://docs.ghost.org/v1/docs/contributing)
- [Ember.js](https://github.com/emberjs/ember.js/blob/master/CONTRIBUTING.md)
- [Ruby on Rails](https://github.com/rails/rails/blob/master/CONTRIBUTING.md)
- [Propostas de William Espindola](https://github.com/portabilis/i-educar/issues/201)

... dentre outros!
