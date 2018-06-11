# Contribuindo

Contribuições são **bem vindas** e sempre são **devidamente creditadas**. Você pode facilmente enviar suas contribuições través de [pull requests](https://help.github.com/articles/about-pull-requests/) no [Github](https://github.com/portabilis/i-educar).

Devido a limitação de tempo, nem sempre estamos disponíveis para responder rapidamente como queríamos, por favor não leve para o lado pessoal.

Antes de enviar qualquer feature ou bug fix:

- Verifique se isto ja não foi solicitado ou ja esta relacionado com alguma issue (aberta ou fechada)  e se alguém ja esta trabalhando nisto;
  - Se não existir, abra uma issue e notifique todos que você esta trabalhando nisto;
  - Se existir, crie um comentário para notificar todos que você esta trabalhando nisto;
- Certifique-se de que o que você precisa ainda não está pronto

## Antes de enviar um Pull Request

** Você deve conhecer sobre [PHP-FIG](https://www.php-fig.org/)** nós usamos padrões como **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - Verifique o estilo do código com: ``vendor/bin/php-cs-fixer fix``.

- **Adicione testes** - Seu path não será aceito se não tiver testes.

- **Documente qualquer comportamento** - Tenha certeza que `README.md` e qualquer outro documento relevante esteja atualizado.

- **Considere nosso ciclo de lançamento** - Nós seguimos [SemVer v2.0.0](http://semver.org/). Quebrar aleatoriamente APIs públicas não é uma opção.

- **Um pull request por feature** - Se você quiser fazer mais de uma coisa, envie várias pull requests.

- ** Envie um histórico coerente** - Tenha certeza que cada commit em seu pull request seja significativo. Se você tivesse que fazer vários commits intermediários enquanto desenvolvendo, or favor [execute um squash](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) antes de submeter.


## Executando destes

``` bash
$ composer test
```

**Bom código**!