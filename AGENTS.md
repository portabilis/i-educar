# AGENTS.md

Orientações para agentes de IA (e pessoas) que trabalham no código do i-Educar.
Este arquivo traz regras e comandos; o conhecimento sobre o projeto fica na
documentação, que é referenciada em cada seção.

## Sobre o projeto

O i-Educar é um software livre de gestão escolar usado por secretarias de
educação e escolas públicas de todo o Brasil: cadastro de alunos, matrículas,
turmas, notas, frequência, servidores, histórico escolar e exportação para o
Educacenso (Censo Escolar do INEP). Veja o [README](README.md).

- Toda a interface, as mensagens, os comentários e os nomes de testes são em
  **português do Brasil**.
- A mantenedora é a [Portábilis](https://portabilis.com.br/). Dúvidas de uso vão
  para o [fórum](https://forum.ieducar.org); bugs e propostas, para as
  [issues](https://github.com/portabilis/i-educar/issues).

## Estrutura do código

O projeto é uma aplicação Laravel que convive com um código legado ainda em
uso. Antes de alterar uma tela, descubra em qual das duas partes ela está.

| Caminho | O que contém |
|---|---|
| `app/` | Código Laravel: models (`app/Models`, os do legado com prefixo `Legacy`), controllers, services, regras de validação (`app/Rules`) e jobs. |
| `ieducar/intranet/` | Telas legadas, servidas pelo `LegacyController` (`routes/web-authenticated.php`). Sufixos: `_lst.php` (listagem, `clsListagem`), `_det.php` (detalhe, `clsDetalhe`) e `_cad.php` (cadastro, `clsCadastro`). As classes base ficam em `ieducar/intranet/include/`. |
| `ieducar/modules/` | Módulos legados (Avaliação, Cadastro, Histórico escolar, API legada em `Api/`). |
| `ieducar/lib/` | Bibliotecas do legado (`CoreExt`, `Portabilis`, `App`). |
| `resources/views/` | Views Blade das telas novas. |
| `database/migrations/` | Migrations organizadas em subpastas. `data/` traz os dados iniciais (como os menus da tabela `public.menus`) e só roda quando `LEGACY_SEED_DATA` está ativo, que é o padrão (ver `config/legacy.php`). |
| `database/factories/` | Factories usadas nos testes. |
| `tests/` | Testes em [Pest](https://pestphp.com/) (ver [Testes](#testes)). |

- Classes `cls*` do legado estão depreciadas: em código novo, prefira os models
  Eloquent e os services de `app/`.
- Nas telas legadas, as propriedades públicas da classe são preenchidas pelos
  nomes dos campos do formulário. **Não renomeie** essas propriedades, mesmo que
  ferramentas de análise apontem o padrão de nome.
- Mudanças no banco sempre por migration nova; nunca edite uma migration que já
  foi publicada.

## Ambiente local

Siga o [guia de instalação](INSTALL.md) (Docker). Os comandos rodam dentro do
container `php`:

```bash
docker compose up -d --build
docker compose exec php composer new-install
```

## Testes

A preparação do banco de testes está no [guia de instalação](INSTALL.md#testes-automatizados):

```bash
docker compose exec postgres createdb testing
docker compose exec php composer testing-install
```

Rode somente os testes afetados pela mudança, passando os arquivos ou um filtro:

```bash
docker compose exec php vendor/bin/pest tests/Unit/Rules/MinhaRegraTest.php
docker compose exec php vendor/bin/pest --filter="trecho do nome do teste"
```

As suítes estão em `phpunit.xml`:

| Pasta | Para que serve |
|---|---|
| `tests/Unit` | Regras, services, models e helpers isolados. |
| `tests/Feature` | Controllers e rotas Laravel. |
| `tests/Request` | Telas legadas exercitadas por HTTP: `GET` abre o formulário, `POST` grava. Os helpers ficam em `tests/Request/Support` (`LegacyForm`, `StudentScenario`, `EmployeeScenario`…). |
| `tests/Pages` | Páginas que só precisam abrir com um usuário logado. |
| `tests/Api` | API usada pelo i-Diário. |
| `tests/Educacenso` | Importação e exportação do Educacenso. |
| `tests/Browser` | Testes de navegador (`composer test:browser`); precisam de um navegador instalado. |

Ao escrever testes:

- Use a sintaxe do Pest (`test('...', function () {...})`), com o nome em
  português descrevendo o comportamento, como `recusa a turma com a hora final
  anterior à inicial`.
- Crie os dados com as factories de `database/factories` e os cenários de
  `tests/Request/Support`; não insira registros com SQL solto.
- Os testes com banco usam `DatabaseTransactions` (ver `tests/Pest.php`); não
  apague nem recrie tabelas.
- Um teste que registra o comportamento atual de um bug conhecido, sem
  corrigi-lo, começa com `defeito:`. Ao corrigir o bug, inverta a asserção e
  remova o prefixo.
- Na CI os testes rodam com `vendor/bin/pest --tia` (`.github/workflows/tests.yml`).

## Padrão de código

- PHP no padrão do [Laravel Pint](https://laravel.com/docs/pint), configurado em
  `pint.json`. Verifique os arquivos alterados com
  `docker compose exec php vendor/bin/pint --test <arquivos>` e corrija com
  `docker compose exec php composer format`.
- Siga o estilo do código ao redor: nomes, densidade de comentários e idioma.
- O SonarCloud analisa cada pull request. Não introduza novos problemas no
  código que você alterou.

## Fluxo de contribuição

O processo completo está no [guia de contribuição](CONTRIBUTING.md). Em resumo:

1. Trabalhe a partir de uma issue. Para mudanças maiores, discuta a solução na
   issue antes de escrever código.
2. Crie uma branch a partir da branch da versão em desenvolvimento (a branch
   padrão do repositório no GitHub, por exemplo `2.12`) e abra o pull request
   contra ela.
3. Um pull request resolve **uma** issue. Referencie a issue na descrição
   (`Resolve #123`) e explique o que mudou e como foi testado, seguindo
   `.github/pull_request_template.md`.
4. Escreva as mensagens de commit conforme o [guia de contribuição](CONTRIBUTING.md).
   Evite commits intermediários; faça squash antes de abrir o PR.
5. Não abra pull requests só com mudanças cosméticas (espaços, indentação).

## Antes de abrir um pull request

- [ ] Os testes dos arquivos alterados passam, e há testes novos para o
      comportamento novo ou corrigido.
- [ ] `vendor/bin/pint --test` passa nos arquivos alterados.
- [ ] Mudanças de banco têm migration (com `down()` quando for possível desfazer).
- [ ] Textos novos na interface estão em português, com a primeira letra
      maiúscula e o restante em minúsculas, exceto nomes próprios e siglas
      (Educacenso, INEP, CPF).
- [ ] A descrição do PR referencia a issue e informa o que foi testado.

## Regras para agentes

- Não altere arquivos fora do escopo da issue.
- Não faça commits, pushes ou abra pull requests sem o pedido de quem está
  conduzindo o trabalho.
- Não adicione dependências novas (Composer ou npm) sem discussão na issue.
- Não exponha dados pessoais: o sistema lida com dados de alunos menores de
  idade. Use dados fictícios nos testes e nos exemplos.
- Se um teste já falhava antes da sua mudança, informe isso em vez de alterá-lo
  para passar.
- Na dúvida sobre uma regra de negócio (Educacenso, avaliação, matrícula),
  pergunte na issue em vez de presumir.

## Outras referências

- [Guia de atualização](UPGRADE.md)
- [Política de segurança](SECURITY.md)
- [Código de conduta](CODE-OF-CONDUCT.md)
- Pacotes que estendem o i-Educar: ver a seção "Pacotes" do [README](README.md#pacotes-módulos).
