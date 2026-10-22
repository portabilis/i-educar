# Changelog

Todas as mudanças relevantes do i-Educar são documentadas neste arquivo.

O formato é baseado no [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/)
e o projeto adota o [Versionamento Semântico](https://semver.org/lang/pt-BR/).

Cada versão agrupa as mudanças nas categorias:

- `Added` para novas funcionalidades.
- `Changed` para mudanças em funcionalidades existentes.
- `Deprecated` para funcionalidades que serão removidas em breve.
- `Removed` para funcionalidades removidas.
- `Fixed` para correções de bugs.
- `Security` para correções de vulnerabilidades.

Veja como atualizar este arquivo no [guia de contribuição](CONTRIBUTING.md#atualizando-o-changelog).

## [Unreleased]

### Added

- Adiciona validação de tamanho mínimo para campo Razão Social ([#1083](https://github.com/portabilis/i-educar/pull/1083))
- Adiciona validações de CEP no frontend ([#1103](https://github.com/portabilis/i-educar/pull/1103))
- feat: adicionar cancelamento de matrícula em lote ([#1143](https://github.com/portabilis/i-educar/pull/1143))

### Changed

- Portabilis patch 02/06/2026 ([#1141](https://github.com/portabilis/i-educar/pull/1141))
- PHP 8.5 ([#1152](https://github.com/portabilis/i-educar/pull/1152))
- Consolida migrations ([#1142](https://github.com/portabilis/i-educar/pull/1142))
- Portabilis patch 09/06/2026 ([#1154](https://github.com/portabilis/i-educar/pull/1154))
- Impede reclassificação na educação infantil e no 1º ano do EF (LDB) ([#1151](https://github.com/portabilis/i-educar/pull/1151))
- Reorganiza migrations ([#1155](https://github.com/portabilis/i-educar/pull/1155))
- Reorganiza migrations de funções ([#1156](https://github.com/portabilis/i-educar/pull/1156))
- Retorna a descrição da situação da matrícula na API ([#1150](https://github.com/portabilis/i-educar/pull/1150))
- refactor: remove migrations de alteração de data do Educacenso ([#1163](https://github.com/portabilis/i-educar/pull/1163))
- Portábilis patch 16/06/2026 ([#1164](https://github.com/portabilis/i-educar/pull/1164))
- build(deps-dev): bump vite from 6.4.2 to 6.4.3 ([#1165](https://github.com/portabilis/i-educar/pull/1165))
- build(deps): bump form-data from 4.0.5 to 4.0.6 ([#1166](https://github.com/portabilis/i-educar/pull/1166))
- Exige CPF no cadastro de pessoa e no cadastro simplificado (pai/mãe/responsável) quando "Exigir CPF" está ativo ([#1159](https://github.com/portabilis/i-educar/pull/1159))
- build(deps): bump actions/cache from 5 to 6 ([#1171](https://github.com/portabilis/i-educar/pull/1171))
- Portabilis patch 01/07/2026 ([#1172](https://github.com/portabilis/i-educar/pull/1172))
- build(deps-dev): bump axios from 1.16.0 to 1.18.0 ([#1184](https://github.com/portabilis/i-educar/pull/1184))
- build(deps): bump actions/checkout from 6 to 7 ([#1170](https://github.com/portabilis/i-educar/pull/1170))
- Portabilis patch 22/07/2026 ([#1185](https://github.com/portabilis/i-educar/pull/1185))
- Portábilis patch 31/07/2026 ([#1191](https://github.com/portabilis/i-educar/pull/1191))
- build(deps): bump postcss from 8.5.15 to 8.5.25 ([#1192](https://github.com/portabilis/i-educar/pull/1192))
- Portabilis patch 11/09/2026 ([#1203](https://github.com/portabilis/i-educar/pull/1203))
- Melhora fluxo de testes ([#1204](https://github.com/portabilis/i-educar/pull/1204))
- Version ([#1205](https://github.com/portabilis/i-educar/pull/1205))
- Torna coerente a nomenclatura relacionada ao recurso de associar um servidor "professor" a turmas ([#1210](https://github.com/portabilis/i-educar/pull/1210))
- Retorna lista de bairros pré-cadastrados e a opção de criar um novo bairro ([#1208](https://github.com/portabilis/i-educar/pull/1208))
- Portábilis patch 13/10/2026 ([#1216](https://github.com/portabilis/i-educar/pull/1216))

### Removed

- Remove tipos e extensão do Postgres ([#1153](https://github.com/portabilis/i-educar/pull/1153))
- Remove configuração sem uso ([#1157](https://github.com/portabilis/i-educar/pull/1157))

### Fixed

- Corrige migrations de view no upgrade de banco existente (DROP redundante sem CASCADE) ([#1160](https://github.com/portabilis/i-educar/pull/1160))
- Corrige download S3: encoding duplo na key + suporte a URLs path-style (MinIO/Wasabi) ([#1049](https://github.com/portabilis/i-educar/pull/1049))
- Valida o tamanho mínimo para campo Nome Fantasia ([#1086](https://github.com/portabilis/i-educar/pull/1086))
- Valida capital social e nome fantasia ([#1085](https://github.com/portabilis/i-educar/pull/1085))
- Valida Capital Social negativo ([#1087](https://github.com/portabilis/i-educar/pull/1087))
- Valida número de Inscrição Estadual ([#1094](https://github.com/portabilis/i-educar/pull/1094))
- fix(curso): validação de horas falta negativas ([#1112](https://github.com/portabilis/i-educar/pull/1112))
- Valida quantidade de etapas no cadastro de curso ([#1167](https://github.com/portabilis/i-educar/pull/1167))
- Valida nome do curso com ao menos uma letra ([#1181](https://github.com/portabilis/i-educar/pull/1181))

### Security

- Resolução de CVEs ([#1193](https://github.com/portabilis/i-educar/pull/1193))

## [2.11.0] - 2026-06-02

Notas completas da versão: [2.11.0](https://github.com/portabilis/i-educar/releases/tag/2.11.0).

### Added

- Novo pacote de relatórios
- Suporte a UID/GID do host no Docker + Customização do NGINX ([#1040](https://github.com/portabilis/i-educar/pull/1040))
- Suporte a logos remotos por URL na geração de relatórios PHPJasper ([#1042](https://github.com/portabilis/i-educar/pull/1042))
- feat(LegacyPerson): inclui a relação entre pessoa e funcionario ([#1048](https://github.com/portabilis/i-educar/pull/1048))
- Adiciona testes unitários MC/DC para o método isPoliOrInstitutionalUser em MessageService ([#1082](https://github.com/portabilis/i-educar/pull/1082))
- Adiciona validação de e-mail com domínio na Pessoa Jurídica (#1078) ([#1093](https://github.com/portabilis/i-educar/pull/1093))
- Adiciona validação de formato de URL no campo Site da Pessoa Jurídica… ([#1089](https://github.com/portabilis/i-educar/pull/1089))

### Changed

- Educacenso 2026
- Tecnologias
  - Laravel 13
  - PHP 8.5
  - Postgres 18
  - Nginx 1.30
- Reescrita do `core`
- Bump form-data from 4.0.0 to 4.0.4 ([#1043](https://github.com/portabilis/i-educar/pull/1043))
- Portábilis patch 23/07/2025 ([#1047](https://github.com/portabilis/i-educar/pull/1047))
- Portábilis Patch 26/08/2025 ([#1052](https://github.com/portabilis/i-educar/pull/1052))
- Bump actions/checkout from 4 to 5 ([#1050](https://github.com/portabilis/i-educar/pull/1050))
- Bump actions/checkout from 4 to 5 ([#1053](https://github.com/portabilis/i-educar/pull/1053))
- Bump vite from 5.4.19 to 5.4.20 ([#1055](https://github.com/portabilis/i-educar/pull/1055))
- Portabilis 19/09/2025 ([#1056](https://github.com/portabilis/i-educar/pull/1056))
- Portabilis patch 29/09/2025 ([#1057](https://github.com/portabilis/i-educar/pull/1057))
- Bump stefanzweifel/git-auto-commit-action from 6 to 7 ([#1062](https://github.com/portabilis/i-educar/pull/1062))
- Bump vite from 5.4.20 to 5.4.21 ([#1069](https://github.com/portabilis/i-educar/pull/1069))
- Melhora a criação de usuários/grupos no Dockerfile ([#1070](https://github.com/portabilis/i-educar/pull/1070))
- Portabilis patch 07/11/2025 ([#1100](https://github.com/portabilis/i-educar/pull/1100))
- Adição de teste para o método Processar() utilizado na classe clsCadastro.inc.php ([#1080](https://github.com/portabilis/i-educar/pull/1080))
- test: Adiciona testes unitários para updateNextEnrollmentsRegistrationDate ([#1081](https://github.com/portabilis/i-educar/pull/1081))
- Bump symfony/http-foundation from 7.3.6 to 7.3.7 ([#1101](https://github.com/portabilis/i-educar/pull/1101))
- Bump actions/checkout from 5 to 6 ([#1113](https://github.com/portabilis/i-educar/pull/1113))
- Portábilis patch 08/12/2025 ([#1115](https://github.com/portabilis/i-educar/pull/1115))
- Bump actions/cache from 4 to 5 ([#1116](https://github.com/portabilis/i-educar/pull/1116))
- Portábilis patch 14/01/2026 ([#1118](https://github.com/portabilis/i-educar/pull/1118))
- Portabilis patch 01/02/2026 ([#1120](https://github.com/portabilis/i-educar/pull/1120))
- Portabilis patch 18/02/2026 ([#1121](https://github.com/portabilis/i-educar/pull/1121))
- Bump rollup from 4.39.0 to 4.59.0 ([#1122](https://github.com/portabilis/i-educar/pull/1122))
- Bump axios from 1.8.2 to 1.15.2 ([#1132](https://github.com/portabilis/i-educar/pull/1132))
- Bump postcss from 8.5.3 to 8.5.14 ([#1133](https://github.com/portabilis/i-educar/pull/1133))
- Bump axios from 1.15.2 to 1.16.0 ([#1137](https://github.com/portabilis/i-educar/pull/1137))
- Bump vite from 5.4.21 to 6.4.2 ([#1127](https://github.com/portabilis/i-educar/pull/1127))
- Bump picomatch from 2.3.1 to 2.3.2 ([#1123](https://github.com/portabilis/i-educar/pull/1123))
- Release ([#1138](https://github.com/portabilis/i-educar/pull/1138))

### Deprecated

- Classes `cls*` estão depreciadas e serão removidas na próxima versão em prol do uso dos models do framework.
- O pacote `ankurk91/laravel-eloquent-relationships` será removido.
- Ocorrerá uma limpeza significativa de `migrations` e da estrutura do banco de dados, tabelas e colunas serão depreciadas.

### Fixed

- Validator e testes para campos de Regras de Avaliação ([#1036](https://github.com/portabilis/i-educar/pull/1036))
- Corrige nomenclatura de configurações para logo dos relatórios ([#1041](https://github.com/portabilis/i-educar/pull/1041))
- fix(cadastro-pf): aplica limite de 100 caracteres no campo nome. #1061 ([#1102](https://github.com/portabilis/i-educar/pull/1102))

## [2.10.0] - 2025-07-02

Notas completas da versão: [2.10.0](https://github.com/portabilis/i-educar/releases/tag/2.10.0).

### Changed

- [Laravel 12](https://laravel.com/docs/12.x)
- [PHP 8.4](https://www.php.net/releases/8.4/en.php)
- [Composer](https://getcomposer.org/changelog/2.8.0)
- [Postgres 17](https://www.postgresql.org/docs/17/index.html)
- [Redis 8](https://redis.io/docs/latest/develop/whats-new/8-0/)
- [Nginx 1.28](https://nginx.org/en/download.html)

## [2.9.0] - 2025-05-06

Notas completas da versão: [2.9.0](https://github.com/portabilis/i-educar/releases/tag/2.9.0).

### Changed

- [Laravel 11](https://laravel.com/docs/11.x)
- [PHP 8.3](https://www.php.net/releases/8.3/en.php)
- [Postgres 16](https://www.postgresql.org/docs/16/index.html)

## [2.8.0] - 2024-06-18

Notas completas da versão: [2.8.0](https://github.com/portabilis/i-educar/releases/tag/2.8.0).

### Changed

- [Laravel 10](https://laravel.com/docs/10.x)
- [PHP 8.2](https://www.php.net/releases/8.2/en.php)
- [Postgres 15](https://www.postgresql.org/docs/15/index.html)

## [2.8.0-beta] - 2023-07-10 [PRE-RELEASE]

Notas completas da versão: [2.8.0-beta](https://github.com/portabilis/i-educar/releases/tag/2.8.0-beta).

### Changed

- [Laravel 10](https://laravel.com/docs/10.x)
- [PHP 8.2](https://www.php.net/releases/8.2/en.php)
- [Postgres 15](https://www.postgresql.org/docs/15/index.html)

## [2.7.5] - 2022-11-10

Notas completas da versão: [2.7.5](https://github.com/portabilis/i-educar/releases/tag/2.7.5).

### Changed

- [2.7] Fallback para a página inicial ([#869](https://github.com/portabilis/i-educar/pull/869))

## [2.7.4] - 2022-07-14

Notas completas da versão: [2.7.4](https://github.com/portabilis/i-educar/releases/tag/2.7.4).

### Changed

- [2.7] Portabilis patch 14/07/2022 ([#866](https://github.com/portabilis/i-educar/pull/866))
  - **Bugs:**
    - [Séries da escola] Verifica vinculo com componentes apenas de turma ativas
    - Corrige problemas da importação do Censo
    - Corrige exportação de componentes de turmas multisseriadas para o Censo escolar

## [2.7.3] - 2022-07-14

Notas completas da versão: [2.7.3](https://github.com/portabilis/i-educar/releases/tag/2.7.3).

### Changed

- [2.7] Inicializa variáveis e remove código morto ([#857](https://github.com/portabilis/i-educar/pull/857))
- [2.7] Inicializa variáveis ([#858](https://github.com/portabilis/i-educar/pull/858))
- [2.7] Portabilis patch 03/07/2022 ([#861](https://github.com/portabilis/i-educar/pull/861))
  - **Melhoria:**
    - Melhora mensagem de confirmação no cancelamento de matrícula
    - Incluir novo tipo de exportação
    - Redireciona as chamadas do index para os atalhos
  - **Bug:**
    - Remove dados nulos do array que converte para string
    - Corrige migração de pareceres da matrícula transferida para nova matrícula
  - **Desenvolvimento:**
    - Atualiza a versão do pacote `dex/composer-plug-and-play`
    - Reorganiza os testes de funcionalidades
- [2.7] Utiliza CDN para importar a lib Prototype.js ([#862](https://github.com/portabilis/i-educar/pull/862))
- [2.7] Bump parse-url from 6.0.0 to 6.0.2 ([#863](https://github.com/portabilis/i-educar/pull/863))
- [2.7] Portabilis patch 10/07/2022 ([#864](https://github.com/portabilis/i-educar/pull/864))
  - **Melhorias:**
    - Melhora mensagem de confirmação no cancelamento de matrícula
    - Incluir novo tipo de exportação
    - Redireciona as chamadas do index para os atalhos
    - Adiciona mensagem de confirmação ao desenturmar
  - **Educacenso:**
    - Atualiza municípios 2022
    - Altera tipo de coluna do orgão regional
    - Grava código regional em caixa alta
    - [Educacenso] Corrige bugs no cadastro de Escolas
  - **Bugs:**
    - Remove dados nulos do array que converte para string
    - Corrige migração de pareceres da matrícula transferida para nova matrícula
    - Corrige função para obter componentes da turma multiseriada
    - Corrige exibição do turno
    - Corrige valor default do match
    - Resolver erro ao fazer soma com string vazia
    - Previne erro ao gravar dados de telefone
    - Corrige bug ao apagar informaçao do tipo de atendimento do aluno
    - Corrige exportação das áreas de conhecimento dos cursos superiores
    - Adiciona validação para possibilidade de dados nulos
  - **Desenvolvimento:**
    - Atualiza a versão do pacote `dex/composer-plug-and-play`
    - Reorganiza os testes de funcionalidades
    - Factories dos menus e permissões de acesso
    - Configura o Laravel Sanctum
    - Estende o usuário padrão do Laravel

### Removed

- [2.7] Remove código morto e inicializa variáveis ([#859](https://github.com/portabilis/i-educar/pull/859))
- [2.7] Remove bibliotecas JavaScript não utilizadas ([#860](https://github.com/portabilis/i-educar/pull/860))

### Fixed

- [2.7] Corrige seeds relativo a obtenção do `dbname` ([#849](https://github.com/portabilis/i-educar/pull/849))
- [2.7] Correções apontadas pelo SonarCloud ([#856](https://github.com/portabilis/i-educar/pull/856))

### Security

- [2.7] Ajustes de segurança ([#854](https://github.com/portabilis/i-educar/pull/854))
- [2.7] Corrige possíveis problemas de segurança ([#855](https://github.com/portabilis/i-educar/pull/855))

## [2.7.2] - 2022-06-26

Notas completas da versão: [2.7.2](https://github.com/portabilis/i-educar/releases/tag/2.7.2).

### Changed

- [2.7] Bump actions/checkout from 2 to 3 ([#851](https://github.com/portabilis/i-educar/pull/851))
- [2.7] Bump actions/cache from 2 to 3 ([#852](https://github.com/portabilis/i-educar/pull/852))
- [2.7] Portabilis patch 26/06/2022 ([#853](https://github.com/portabilis/i-educar/pull/853))
  - **Educacenso:**
    - Remove campo 'conveniada com o poder publico'
    - Remove campo Forma(s) de organização do ensino
    - Adicionar opções nas dependências da escola
    - Poder publico parceria convenio
    - Adicionar campo "Formas de contratação entre a Administração Pública e outras instituições"
    - Remover obrigatoriedade do campo "Rede local de interligação de computadores"
    - Alterar regras nos campos de número de salas
    - Alterar regra do campo de Equipamentos da escola
    - Alterar campo Instrumentos, materiais socioculturais e/ou pedagógicos
    - Alterar estrutura da aba de Recurso do cadastro de escolas
    - Altera campo "Função exercida" no vínculo professor turma
    - Mudanças do registro 00 - Incluir campos de números de matrículas atendida pelo convênio
    - [Educacenso 2022] Release dos registro00 e registro10
    - Release das alterações do  Registro00 do Educacenso
    - Release das alterações do registro 10 do Educacenso
    - Registro 40 - Tela de análise
    - Mudanças do registro 40 - Alterar regra do Tipo de vínculo
    - [Educacenso 2022] Release da alterações do registo 40
    - [Educacenso 2022] Mudanção do registro 50 - Adicionar campo Unidade(s) curricular(es) que leciona
    - [Educacenso 2022] Registro 60 - Cria tela de cadastro do Itinerário da enturmação
    - Corrige ortografia da mensagem
    - [Educacenso 2022] Registro 30 - Implementa cadastro de Pós-graduações concluídas
    - Corrige origem do codigo da religiao
    - [Educacenso] Exportação do Registro 20
    - [Educacenso] - Exportação do Registro 10
    - [Educacenso 2022] Registro 20 - Correção do campo Formas de organização da turma
    - [Educacenso] - Exportação do Registro 30
    - [Educacenso] Adiciona nova área de conhecimento - Disciplinas
    - [Educacenso] Exportação do registro 50 - Versão 2
    - [Educacenso] Exportação do registro 60 - Versão 2
    - [Educacenso] Corrige problema ao popular tabela de pós graduações com o valor 4
    - [Educacenso] Registro 40 - Arquivo de exportação
    - [Educacenso] Corrige problema ao gravar informações de Itinerário formativo do aluno
    - [Educacenso] Exportação do registro 00
    - Cria migration para habilitar exportação do educacenso
    - Troca ordem de checagem de gestores
    - [Educacenso] Corrige valor do campo "Nao possui pos-graduacao"
    - Adiciona novas opções de curso superior
    - Cria migration para atualização de instituições de ensino.
    - [Educacenso] Registro 20 - Corrige busca de disciplinas com docentes vinculados
    - [Educacenso] Registro 20 - Mudanças referentes ao Layout 5
    - [Educacenso] Registro 60 - Correção da análise
    - [EDUCACENSO] Atualiza as arquivo de atividades complementares
  - **Melhorias:**
    - Melhora ações na tela de series da escola
    - Adiciona validação da campo na busca da cidade
    - Implementa bloqueio de matricula apos a data de fechamento
    - Implementa validação das datas em anos letivos anteriores
    - Melhora processo de salvamento do turno
    - Adiciona regra para desabilitar o campo corretamente
    - Adiciona nova maneira de formatação de mascara de CNPJ
  - **Correções:**
    - Corrige calculo de média sem arredondamento
    - Corrige parâmetro para definir componentes em etapas específicas
    - Corrige duplo clique na enturmação em lote
    - Identifica a serie em componentes repetidos na turma multiseriada
    - Ajusta regra de busca de matriculas na regra Frequência da Regra de Ciclos
    - Corrige leitura dos campos para interpretar valor vazio
    - Corrige salvamento quando não há o recurso no cadastro
    - Corrige condição para matrículas tipo Atividade Complementar ou AEE
    - Corrige erro ao tentar gravar com valor vazio
    - Corrige carregamento de dados quando ocorrer erro de validação
    - Corrige gravação de dados da escola
    - Corrige validação do campo de formas de contratação
    - Corrige comportamento para a tabela conservar configuração
    - Atualiza lista de funções exercidas
    - Corrige situação onde a media arredondada ou a media esteja vazia
    - Erro ao somar nota conceitual quando esta sem preenchimento
    - Corrige erro ao somar com variáveis vazias
    - Verifica se o servidor está ativo
  - **Desenvolvimento:**
    - Utiliza o schema do projeto
    - Cria nova exception para evitar flood de erros no desnecessários no honeybadger
    - Adiciona na classe para ignorar erros no honeybager

## [2.7.1] - 2022-06-26

Notas completas da versão: [2.7.1](https://github.com/portabilis/i-educar/releases/tag/2.7.1).

### Added

- [2.7] Adiciona a configuração do `dependabot.yml` ([#844](https://github.com/portabilis/i-educar/pull/844))

### Changed

- [2.7] Portabilis patch 06/05/2022 ([#842](https://github.com/portabilis/i-educar/pull/842))
  - **Educacenso:**
    - Altera data de referência para o Educacenso 2022
    - Mudanças do registro 20 - Alterações no campo de Etapa de ensino
    - Adicionar novo campo "Formas de organização da turma" no cadastro de turmas
    - Adicionar novo campo "Unidade curricular" no cadastro de turmas
    - Adicionar novo campo "Estrutura curricular" no cadastro de turmas
    - [Educacenso 2022] Altera labels
    - Alterações para o novo layout do Registro20 do Educacenso
    - Mudanças no registro 20 - Atualizar campo "Formas de organização da turma" - Versão 3 do layout
    - Adiciona validação do Educacenso no campo tipo de atendimento
    - Desabilitar a exportação do arquivo do Censo em todos os clientes
  - **Melhorias:**
    - Adiciona campo código da rede estadual no exportador
    - Filtra alunos pela situação da matrícula
    - Adiciona campo Raça na seção aluno no exportador de dados
  - **Bugs:**
    - Corrige erro de conversão de float para string na regra de casas decimais
    - Exibe as datas da enturmação corretamente
    - Corrige endpoint de enturmações
    - Salva a descrição ao criar uma nova série
    - Traz a informação do SUS após cadastro de pessoa física
    - Retorna a data de alteração do nível de acesso do usuário
    - Remover o servidor disciplina que não contém função
    - Adiciona obrigatoriedade do campo de função servidor no cadastro de servidor disciplina
    - Corrige campo das etapas que o curso contém
    - Corrige o problema onde não possível cadastrar um novo usuário
    - Impede sistema de remover label de campos com mesmo nome, mas id difeferente
    - Corrige titulo da guia de Atualização de históricos
    - Corrige validações dos campos e etapa de ensino
    - Corrige campo Raça onde perdia valor após falha de validação
    - Adiciona checagem de variável
    - Corrige validações do educacenso
    - Corrige menu superior na tela de edição dos cadastros de "Bibliotecas"
    - Corrige o funcionamento da regra de Frequência da Regra de Ciclos
    - Correções na tela de consulta do movimento geral
    - Verifica se existe parâmetro antes do cast
    - Ajustado filtro "Situação" e opção "Exceto Transferidos/Abandonos"
  - **Desenvolvimento:**
    - Altera o campo ref_cod_funcao da tabela servidor_disciplina para NOT NULL
    - Permite definir fechMode antes de executar a consulta
    - Adiciona cache no workflow na instalação dos pacotes
    - Melhora factories de matrícula e turma
    - UI
    - Atualizado lib Chosen
    - Adiciona suporte ao Composer 2.3
    - Remove arquivos JS inúteis
    - Remove CSS inúteis
- [2.7] Define as permissões de acesso do GitHub Actions ([#843](https://github.com/portabilis/i-educar/pull/843))
- [2.7] Bump guzzlehttp/guzzle from 7.4.2 to 7.4.3 ([#845](https://github.com/portabilis/i-educar/pull/845))

## [2.7.0] - 2022-05-04

Notas completas da versão: [2.7.0](https://github.com/portabilis/i-educar/releases/tag/2.7.0).

### Changed

- [PHP 8](https://www.php.net/releases/8.0/en.php)
- [Postgres 14](https://www.postgresql.org/docs/14/index.html)
- [Laravel 9](https://laravel.com/docs/9.x)
- [2.7] Portabilis patch 29/04/2022 ([#838](https://github.com/portabilis/i-educar/pull/838))
  - **Melhorias:**
    - Aumenta o número máximo de caracteres do complemento do endereço
    - Progride matrícula mesmo sem lançamentos em disciplinas não reprovativas
    - Atualiza código para novas funções do PHP 8
    - Inclui bloqueio e mensagem de aviso ao tentar modificar data de saída do aluno
    - Configura urls dos assets para usar HTTPS
    - Adiciona informações na API para o I-Diário
    - Cria migration para normalizar os dados de turmas excluídas
    - Adiciona validação de obrigatoriedade de regra de avaliação da série
  - **Bugs:**
    - Resolve problema com assets HTTP
    - Ajusta nomenclatura do município Mogi das Cruzes
    - Ajusta count para realizar paginação corretamente
    - Faz verificação se classe esta instanciada
    - Corrige erros referente ao PHP 8
    - Faz checagem se é null na chamada do controller
    - Corrige erros do PHP 8
    - Ajusta erros do PHP 8
    - Inicializa variáveis  para evitar erros
    - Faz verificação de retorno e refatora chamada de função
    - Corrige maneira que faz o count
    - Faz cast para inteiro para evitar erro na saída do calculo.
    - Faz verificação de número
    - Realiza verificação de data valida
    - Adiciona verificação se o retorno é uma lista
    - Ajusta parametro id enrollments-enroll
    - Evita o remanejamento caso enturmação seja na mesma turma
    - Apresenta apenas componentes do ano da turma
    - Adiciona validação de tipo
    - Remove parâmetro não utilizado e adiciona validação de existência da model
    - Inicia variável
    - Faz validação de existência de variável
    - Corrige validação de exclusão de todas as series
    - Considera como ultima etapa em disciplinas nao reprovativas
    - Ajusta parâmetro matrícula na requisição
    - Corrige atualização de dados da turma
    - Apresenta descrição do curso e da série na listagem e detalhes da sequência de enturmação.
  - **Desenvolvimento:**
    - Altera versões no GitHub Actions
    - Remove código não utilizado
    - Define variáveis e melhora a leitura da função
    - Remove o ignore do php e adiciona a instalação do yarn
    - Adiciona escopos nos models
    - Faz update da versão do PHP do container docker
- [2.7] Atualiza o readme e Docker ([#839](https://github.com/portabilis/i-educar/pull/839))

## [2.6.9] - 2022-04-29

Notas completas da versão: [2.6.9](https://github.com/portabilis/i-educar/releases/tag/2.6.9).

### Changed

- [2.6] Bump follow-redirects from 1.14.7 to 1.14.8 ([#828](https://github.com/portabilis/i-educar/pull/828))
- [2.6] Bump node-forge from 1.2.1 to 1.3.0 ([#830](https://github.com/portabilis/i-educar/pull/830))
- [2.6] Bump minimist from 1.2.5 to 1.2.6 ([#831](https://github.com/portabilis/i-educar/pull/831))
- [2.6] Bump guzzlehttp/psr7 from 2.1.0 to 2.2.1 ([#832](https://github.com/portabilis/i-educar/pull/832))

### Fixed

- [2.6] Corrigindo a cor da apresentacao dos dias nao letivos no calendario ([#823](https://github.com/portabilis/i-educar/pull/823))

## [2.6.8] - 2022-01-28

Notas completas da versão: [2.6.8](https://github.com/portabilis/i-educar/releases/tag/2.6.8).

### Added

- @ralphg6 fez sua primeira contribuição [#814](https://github.com/portabilis/i-educar/pull/814)
- @luizreginaldo fez sua primeira contribuição [#817](https://github.com/portabilis/i-educar/pull/817)

### Changed

- [2.6] Torna o título do i-Educar padrão por @marcosoliveirasoares94 [#811](https://github.com/portabilis/i-educar/pull/811)
- [2.6] Portabilis patch 11/12/2021 por @edersoares [#810](https://github.com/portabilis/i-educar/pull/810)
  - **Educacenso:**
    - Corrige bug da Consulta 1ª fase - Matrícula inicial.
  - **Melhorias:**
    - Altera label 'Nome social' para 'Nome social e/ou afetivo'.
    - Adiciona Modal na coluna de componentes curriculares.
    - Inclui comando Artisan para Ativar e Desativar login de Funcionários.
    - Troca a mensagem padrão de erro 500.
    - Apresenta apenas os cursos referente à função do servidor.
    - Remove botão e função de editar responsável no cadastro de aluno.
    - Auto-seleciona a opção da lista caso venha apenas um resultado.
    - Adiciona favicon.
    - Remove tags html incompatíveis com o jasper.
    - Adiciona possibilidade de selecionar regra de avaliação na seleção de matrículas.
  - **Bugs:**
    - Filtra parâmetros passados para remover função do servidor.
    - Corrige apresentação do botão de excluir.
    - Adiciona validação de tipos em algumas condicionais.
    - Corrige erro de edição de cadastros de PFs pela opção detalhada de Aluno.
    - Corrige bug que impedia o cadastro do quadro de horários.
    - Corrige passagem incorreta de parâmetro.
    - Ajusta o campo 'Cidade' no cadastro manual de históricos para autocomplete.
    - Ordena a listagem de modelos de boletins e implementa plugin de simple search.
    - Aplica filtro de turmas e adiciona processamento de enturmações para manter a cronologia.
    - Adiciona verificação de index do array.
    - Ajusta scape string do nome do cadastro da pessoa.
    - Correção na view de históricos extra curriculares.
  - **Desenvolvimento:**
    - Adiciona Lerna ao projeto.
    - Atualiza a versão do Lighthouse.
    - Adiciona função para excluir view materializada.
    - Atualiza view materializada.
- [2.6] (fix) composer.json: Update dex/composer-plug-and-play por @ralphg6 [#814](https://github.com/portabilis/i-educar/pull/814)
- [2.6] Bump follow-redirects from 1.14.4 to 1.14.7 por @dependabot [#816](https://github.com/portabilis/i-educar/pull/816)
- [2.6] Altera o valor de `CACHE_DRIVER` no arquivo .env.example por @luizreginaldo [#817](https://github.com/portabilis/i-educar/pull/817)
- [2.6] Bump nanoid from 3.1.30 to 3.2.0 por @dependabot [#818](https://github.com/portabilis/i-educar/pull/818)
- [2.6] Bump node-fetch from 2.6.6 to 2.6.7 por @dependabot [#819](https://github.com/portabilis/i-educar/pull/819)
- [2.6] Portabilis patch 23/01/2022 por @edersoares [#820](https://github.com/portabilis/i-educar/pull/820)
  - **Melhorias**
    - Altera espaços dos históricos para não quebrar página.
    - Aplica decode HTML no título da página.
    - Adiciona controle de bloqueio de mudança de status da matrícula.
    - Bloqueia campo ano quando existe matrícula.
    - Aplica validação numérica no campos Número da carteira do SUS.
    - Adiciona lista para ignorar validação de CPFs.
  - **Bugs**
    - Corrige situação onde comparava erroneamente a quantidade de disciplinas com dependência.
    - Adiciona cast para inteiro para evitar erro na emissão de histórico.
    - Altera maneira de buscar séries.
    - Ajusta o retorno da propriedade para que substituia `\n` e `\r` por `<br />` para evitar erros de parse JSON.
    - Apresenta corretamente o nome da regra de avaliação.
  - **Desenvolvimento**
    - Ajusta a versão do Composer nos testes.
    - Remove construtor pois era executado antes da tabela existir ao executar as migrations.
    - Atualiza Composer Plug and Play.
    - Publica assets do Laravel Horizon.
- [2.6] Ajustes de versão ([#821](https://github.com/portabilis/i-educar/pull/821))

### Removed

- [2.6] Remove duplicação do nome i-Educar dos títulos das páginas por @marcosoliveirasoares94 [#809](https://github.com/portabilis/i-educar/pull/809)

## [2.6.7] - 2021-11-04

Notas completas da versão: [2.6.7](https://github.com/portabilis/i-educar/releases/tag/2.6.7).

### Added

- Adiciona função para remover faltas e atrasos de servidor ao removê-lo.
- Adiciona campos faltantes no detalhe dos uniformes do aluno.
- Adiciona filtro para evitar o erro no cadastro de informações adicionais do servidor.
- Adiciona função para remover faltas e atrasos de servidor ao removê-lo.
- Adiciona try-catch na montagens dos campos de situação e limpa o código.
- Adiciona try-catch na montagens dos campos de situação e limpa o código.

### Changed

- Ordena matrículas pelo nome da serie.
- Aplica style code via GitHub Actions.
- Configura o projeto para forçar o HTTPS em todas as chamadas de acordo com configuração.
- Reescreve as `factories`.
- Reorganiza factories.
- Style code.
- Aplica style code via GitHub Actions.
- Reorganiza factories.
- Configura o projeto para forçar o https em todas as chamadas de acordo com configuração.
- Reescreve as `factories`.

### Removed

- Remove obrigatoriedade de CNPJ no cadastro e edição de pessoa jurídica.
- Remove classe inútil do projeto.
- Remove classe inútil do projeto.

### Fixed

- Adiciona filtro para evitar o erro no cadastro de informações adicionais do servidor
- Corrige definição de parâmetro.
- Corrige o sql do DELETE.
- Remove string vazia do slug evitando problemas de pesquisa.
- Declara variável corretamente.
- Corrigedo bug que permitia que dados da turma, como escola curso e séries fossem alterados mesmo tento enturmações.
- Permite edição de turmas quando for `padrao_ano_escolar`.
- Corrige erro de sintaxe na API de cursos.
- Correção no cancelamento de transferência da matrícula.
- Corrige código da escola do histórico de transferência.
- Inclui os campos da aba "Dados adicionais" do cadastro de servidor para serem selecionados no exportador de dados.
- Corrige definição de parâmetro.
- Corrige o sql do DELETE.
- Corrige comandos do plug and play #802.

## [2.6.6] - 2021-10-11

Notas completas da versão: [2.6.6](https://github.com/portabilis/i-educar/releases/tag/2.6.6).

### Added

- Inclui destaque de página ativa nas paginações.
- Adiciona gestores escolares na unificação de pessoa.
- Implementa cadastro de pai e mãe através de modais simplificadas.
- Adiciona cache nas chamadas de menus por usuários.

### Changed

- Altera labels nas telas de unificação de pessoas e alunos.
- Padroniza consultas do movimento mensal.
- Atualiza informação de espaço sobrando no histórico escolar.
- Altera filtro para buscar informações pelo ano corrente.
- Create SECURITY.md #794.
- Bump nth-check from 2.0.0 to 2.0.1 #797.
- Bump ansi-regex from 5.0.0 to 5.0.1 #798.
- Script para unificar alunos de uma lista e pessoas.
- Fixa a versão do Postgres para evitar problemas no build.
- Fixa a versão do Composer Plug and Play.
- Atualiza versão do Composer Plug and Play.

### Removed

- Remove data de exclusão para aluno ativo.
- Remove parâmetros desnecessários ao excluir tipo de abandono.
- Remove obrigatoriedade de campos cuja função não é obrigatória.

### Fixed

- Corrige `data_exclusao` ao Editar aluno apenas se vier do método excluir.
- Adiciona exclusão de servidor disciplina ao excluir um servidor.
- Estrutura do banco de dados para a busca ativa.
- Ajusta reclassificação alfabética para ignorar alunos desenturmados em lote.
- Corrige erro 500 ao tentar editar cadastro de usuário.
- Corrige o nome do campo que deve ser true quando há uma transferência.
- Replica pareceres gerais para todos os componentes da matrícula no editor de texto.
- Ajusta apresentação dos calendários na tela de emissão do Movimento Mensal.
- Permite buscar por nome quando não tem slug informado no cadastro de pessoa.
- Adiciona variável oculta para manter compatibilidade com a alteração de funcionalidade do ano letivo.
- Corrige nome de variável.
- Garante que os IDs duplicados serão armazenados como array.
- Faz cast para array no resultado da busca para salvar corretamente.
- Preenche corretamente campo oculto de ano letivo.
- Corrige parâmetros da função implode que estavam trocados.
- Corrige escrita incorreta de parâmetro.
- Corrige sequencial da tabela de raça.
- Corrige inconsistência na chamada da regra de avaliação.
- Corrige exibição de alunos remanejados na tela de movimento mensal.
- Corrige situação de acesso a tipos de variáveis não definidas.
- Correções de definições de variáveis.
- Atribui valores zerados quando não estão definidos para calculo.
- Evita erro caso não o calendário letivo não esteja definido na turma.
- Ajusta variável para busca de aluno.

## [2.6.5] - 2021-08-31

Notas completas da versão: [2.6.5](https://github.com/portabilis/i-educar/releases/tag/2.6.5).

### Changed

- Ajustes de nomenclatura em "Configurações do Sistema".
- Estrutura do banco de dados para a busca ativa #780.
- Reordena migrations das configurações do sistema #783.

### Removed

- Remove implementação que deixa sempre o campo conveniadaPoderPublico em branco.
- Remove função `toLatin1`.

### Fixed

- Faz checagem de funcionário e usuário para determinar ação correta do sistema.
- Corrige SQL e unifica regra na consulta do Movimento Geral.
- Corrige situação em que os botões de ação sumiam quando a tela apresentava erro.
- Garante cópia do tipo de falta correto.
- Ativa cadastros de tipos do abandono #785.

## [2.6.4] - 2021-08-24

Notas completas da versão: [2.6.4](https://github.com/portabilis/i-educar/releases/tag/2.6.4).

### Added

- Adiciona fluxo de validação de servidor no cadastro de função.
- Adiciona link para edição de e-mail no cadastro da escola.

### Changed

- Ajusta validação de campos do Educacenso.
- Melhoria na tela de unificação de alunos.
- Ajusta migrations para não fixar ID de categorias de configurações e evitar futuros problemas.
- Atualiza bibliotecas do projeto.
- Substitui Facade de assets ao projeto.

### Removed

- Remove espaço ao montar o arquivo de exportação do Educacenso.

### Fixed

- Valida se tem itens marcados para exportação do Educacenso.
- Remove tags HTML proibidas dos pareceres descritivos.
- Corrige validação de preenchimento de tela de cadastro de escola.
- Corrige processamento de histórico ao realizar uma transferência.
- Corrige situação em que não permitia criar uma escola.

## [2.6.3] - 2021-08-11

Notas completas da versão: [2.6.3](https://github.com/portabilis/i-educar/releases/tag/2.6.3).

### Added

- Permite que a importação do Censo receba arquivos de 2021.
- Permite matrículas na mesma série em cursos de atividade complementar.
- Adiciona campo de "data de saída da matrícula" na exportação de dados.
- Cria chave de relacionamento entre tabelas de servidor, função e disciplinas.
- Adiciona configuração para ativar a exportação do arquivo do Educacenso [#775](https://github.com/portabilis/i-educar/pull/775).

### Changed

- Analisa CNPJ da escola privada .
- Possibilitar ao admin configurar dias para expirar/inativar usuário que não acessa o sistema.
- Atualiza assets do Laravel Horizon.
- Torna configurável o boletim do professor [#774](https://github.com/portabilis/i-educar/pull/774).

### Removed

- Remove obrigatoriedade de CNPJ no cadastro de pessoa jurídica.
- Remove parâmetros opcionais que estão antes de parâmetros obrigatórios (iniciativa de migração PHP 8).

### Fixed

- Corrige nomenclatura de dados do Educacenso.
- Corrige erro na edição de escola.
- Corrige unificação de servidor (pessoa).
- Corrige erro ao inserir um compromisso na agenda.
- Ajusta lógica para permitir cadastrar um novo usuário sem validar a senha antiga.
- Ajusta comportamento do campo "Data de saída" das matrículas para atualizar automaticamente.
- Corrige inconsistência nos dados de disciplinas relacionadas à funções do servidor.
- Ajusta comportamento do filtro de consulta do cadastro de turmas.
- Ajusta preenchimento de campo pós busca.
- Corrige chamada de método incompatível com PHP 8 (iniciativa de migração PHP 8).

### Security

- Possibilitar ao admin incluir padrões de segurança na definição da senha.

## [2.6.2] - 2021-07-21

Notas completas da versão: [2.6.2](https://github.com/portabilis/i-educar/releases/tag/2.6.2).

### Added

- Adiciona opção "Outras" entre as deficiências do Educacenso.
- Adiciona funcionalidade de Busca Ativa.
- Inclui o campo de descrição nos cadastros de cursos e séries
- Permite editar a data de retorno em detalhes do histórico de afastamento do servidor.

### Changed

- Atualiza a lista de cursos superiores conforme.
- Atualiza atividades complementares.
- Migração para atualizar as instituições de ensino superior das graduações dos gestores.
- Popula a tabela que contém as configurações do i-Educar #769, #773.
- Não salva data de exclusão ao editar usuário.
- Ajusta o campo dias letivos para permitir mais caracteres.
- Exibe botão "Remanejar" para todos os casos que há mais de uma enturmação.
- Apresenta descrição dos cursos e series nas listas e _selects_ de turmas, séries da escola e componentes da série.
- Envia por API a situação das unidades escolares.
- Aumenta o range do campo de faltas da regra de avaliação.
- Roda testes automatizados na branch `main`.
- Agrupa validações comuns no cadastro de aluno.
- Configura o throttle nos middlewares e login.

### Fixed

- Valida para não permitir que o código INEP comece com número zero.
- Boletim do professor não é gerado no iDiário #772.
- Remove espaços que causam erros no banco de dados.
- Corrige problema de permissão de acesso em telas sem `process`.
- Corrige erro ao editar escola.
- Corrige a  apresentação de instituição na edição de tipo de dispensa.
- Mantém o aluno como cursando caso não tenha nenhuma média.

## [2.6.1] - 2021-05-31

Notas completas da versão: [2.6.1](https://github.com/portabilis/i-educar/releases/tag/2.6.1).

### Added

- Inclui campo Naturalidade e Nacionalidade do aluno, no exportador de dados.
- Permite adicionar mais de uma matrícula ao servidor.
- Adiciona observações na matrícula.
- Adiciona a URL assinada.
- Adiciona um filtro de ativo na busca do histórico escolar.
- Inclui o campo modalidade de ensino no exportador de dados.
- Permite consultar o município pelo código.
- Adiciona função de exclusão de afastamento de servidor.
- Adiciona campos de busca na pesquisa de alunos transferidos.
- Adiciona regra para 'Voltar para "Em espera"' quando matriculas forem canceladas.
- Adiciona label `report.termo_exame_final` no seeder inicial.
- Adiciona listener no envio de e-mail para marcá-lo como transacional.
- Adiciona feature para customizar os botões.
- Adiciona yarn.lock file.

### Changed

- Altera data de referência do Educacenso 2021.
- Define título e número de processo da tela de sequência de enturmação.
- Recurso de modalidade de ensino.
- Apresentar toda as matriculas da turma ao editar a sequência de fechamento.
- Modifica _view_ de `info_enrollment` para adicionar novas regras.
- Horizon assets.
- Carrega funções antes do carregamento para estarem disponíveis no `onchange` do curso.
- Atualiza campo de função na tabela de servidor disciplina.
- Bump `dns-packet` from `1.3.1` to `1.3.4` #764.
- Bump `ws` from `7.4.5` to `7.4.6` #765.
- Atualiza dependências #766.

### Removed

- Remove retorno tipado das funções.

### Fixed

- Corrige o instalador em servidor [#757](https://github.com/portabilis/i-educar/pull/757).
- Verifica se a pessoa física principal ainda existe.
- Corrige campo de faltas globalizadas.
- Remove tratamento de string para que a busca seja feita com acentuação.
- Move para arquivos extras CSS e JS da documentação padrão.
- Permite fazer a matrícula mesmo sem copiar notas.
- Permite alterar a senha corretamente na página "Meus dados".
- Corrige paginação de telas.
- Corrige pessoa logada na pesquisa de quadro de horários.
- Ajusta a função que busca a data base de remanejamento do cadastro da instituição.
- Corrige erro 500 ao tentar unificar Pessoa Física.
- Corrige a troca da foto de perfil.
- Corrige erro 500 ao tentar copiar uma regra de avaliação.
- Atualiza a foto do perfil apenas quando a pessoa física for a mesma que estiver logada no sistema.
- Ajusta URL dos arquivos.
- Move JS para arquivo externo e aplicar JS externo na view.
- Habilita campos para edição de pessoa física simplificado.
- Lança erro 404 ao invés de 500 em página não encontradas.
- Corrige disciplinas dos históricos na unificação.
- Corrige situação de nacionalidade de naturalidade.
- Adiciona escape string em input de texto.
- Atualiza data de cancelamento.
- Adiciona escape_string nos parâmetros de busca.
- Apresentar toda as matriculas da turma ao editar a sequência de fechamento.
- Trata a string corretamente antes de persistir no banco de dados.
- Ignora alocações com data de saída no quadro de horários.
- Corrige funcionalidade de historico dos inputs.

## [2.6.0] - 2021-03-17

Notas completas da versão: [2.6.0](https://github.com/portabilis/i-educar/releases/tag/2.6.0).

### Added

- Incluir editor de texto e apresentação de pareceres enviados do i-Diário com formatação em "Faltas e notas".
- Inclui informação do CPF na exportação de usuários.
- Implementa nova abordagem de renderização de view para código legado.

### Changed

- [Laravel 8](https://laravel.com/docs/8.x).
- [Postgres 13](https://www.postgresql.org/docs/13/index.html).
- Mudanças arquiteturais e limpeza de arquivos.
- Melhora a rotina de unificação de Pessoas/Alunos.
- Ajusta query para que não seja obrigatório a escola no filtro.
- Move JavaScript do arquivo PHP para arquivo externo.
- Utiliza GitHub Actions para _continuous integration_.
- Executa testes de forma paralela.
- Atualiza variáveis para o funcionamento correto do Xdebug.
- Recria as triggers de auditoria.

### Removed

- Remove algumas chaves estrangeiras da tabela documento.
- Remove dependência em prol de `Str::slug`.
- Remove [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) em prol do [Laravel Telescope](https://laravel.com/docs/telescope).
- Remove [Laravel Dusk](https://laravel.com/docs/dusk) por não haver uso.

### Fixed

- Apresenta dados de aluno mesmo que não tenha documento.
- Lança exception quando não há regra de avaliação configurada para a nova matricula.
- Corrige importador do Educacenso para o ano de 2020.
- Corrige testes após correção de estilo.

## [2.5.4-upgrade] - 2021-03-17 [PRE-RELEASE]

Notas completas da versão: [2.5.4-upgrade](https://github.com/portabilis/i-educar/releases/tag/2.5.4-upgrade).

### Changed

- Prepara o i-Educar para upgrade do banco de dados #756.

## [2.5.3] - 2021-02-16

Notas completas da versão: [2.5.3](https://github.com/portabilis/i-educar/releases/tag/2.5.3).

### Added

- Permite usar simple search de escolas na tela de atualização dos históricos escolares.
- Adiciona parâmetros para ignorar owner e privileges.

### Changed

- Removido label e obrigatoriedade de preenchimento dos campos "Nome da escola", "Estado da escola" e "Município da escola".
- Apresenta endereço da instituição quando escola não informada.
- Atualiza assets.
- Atualiza telescope assets.

### Removed

- Remove arquivo antigo sem utilidade.

### Fixed

- Ajusta forma de obter a enturmação da matrícula no faltas e notas.
- Ajustar campos de informações da escola ao retornar mensagem de erro e salvar sem preenchimento em solicitação de transferência.
- Usa helper que mostra escolas sem filtro por usuário.
- Adiciona uma condição para a situação de reprovado por faltas.
- Corrigindo lógica de exibição dos dados do INEP.
- Atribui o valor correto do `cod_curso` caso não venha do form.
- Atualiza apenas as matriculas que possuem enturmações.
- Altera label do campo e insere labelhint.
- Corrigido bug do `window.opener null` no Firefox e versões mais recentes do Chrome.

## [2.5.2] - 2021-01-27

Notas completas da versão: [2.5.2](https://github.com/portabilis/i-educar/releases/tag/2.5.2).

### Added

- Inclui labelhint e placeholder nos campos de data.
- Permite usar simple search de escolas na tela de transferência dos alunos.
- Incluindo plugin de feedback de numero de caracteres disponíveis.
- Adiciona endpoint para alteração do código de aluno estadual.
- Cria um helper para atender a validação de data no formato dia/mês.
- Cria helper que traz todas as escolas sem restrições por usuários.

### Changed

- Ordena regras de avaliação pelos últimos anos.
- Exibe observações de transferência.
- Melhora view `info_enrollment` para não pegar as turmas inativas.
- Horizon assets.
- Renomeia a migration de funções de auditoria #746.
- GitHub Actions #747.
- GitHub Actions badge #748.

### Fixed

- Altera tipo de campo para text para possibilitar mais caracteres.
- Aumenta o timeout do job de dispensa em lote.
- Corrige o processamento do histórico quando há dispensas.
- Corrige validação de campo vazio nos detalhes do aluno.
- Corrige detalhe da matrícula quando em abandono.
- Filtra por ano matriculas do regime cíclico.
- Corrige os códigos do país para o do Brasil #743.
- Migra ID do Brasil nos estados brasileiros #745.

## [2.5.1] - 2021-01-04

Notas completas da versão: [2.5.1](https://github.com/portabilis/i-educar/releases/tag/2.5.1).

### Fixed

- Corrige o instalador [#741](https://github.com/portabilis/i-educar/pull/741).
- Corrige o boletim escolar e o parecer descritivo https://github.com/portabilis/i-educar-reports-package/pull/82.

## [2.5.0] - 2020-12-29

Notas completas da versão: [2.5.0](https://github.com/portabilis/i-educar/releases/tag/2.5.0).

### Added

- Implementa forma de desconsiderar algumas disciplinas para progressão do aluno.
- Adiciona estado civil "Não informado".
- Permite pegar a soma das cargas horárias das disciplinas ao processar histórico escolar.
- Permite desconsiderar a frequência na aprovação/reprovação.
- Permite configurar a permissão do arquivo de log.
- Cria view `info_enrollment`.

### Changed

- Laravel 7.
- Simplificação das imagens Docker [#739](https://github.com/portabilis/i-educar/pull/739) e [#740](https://github.com/portabilis/i-educar/pull/740).
- Inúmeras melhorias e correções.
- Limpeza e diminuição do código fonte.
- Não permite que módulos com mesmo nome e etapas sejam cadastrados.
- Aumenta tamanho permitido do envio de arquivos(Fotos/Documentos).
- Utiliza o plugin `Composer Plug and play`.
- Diversas remoções de código inútil.
- Substitui `select convencional` por `simple search` para escolas.
- Ordena consulta de matriculas para pegar sempre ultima enturmação (ativa ou não).
- Altera a versão do `Composer` na imagem `Docker PHP`.

### Removed

- Remove suporte ao PHP 7.3.
- Remove classes e página de auditoria.
- Remove arquivos desnecessários.

### Fixed

- Implementa validações necessárias e padroniza mensagens de erro na tela de dispensa em lote.
- Limita número de notificações retornadas.
- Corrige bloqueio de lançamento de notas.
- Apresenta todos os anos letivos registrados na escola como opções na tela de Componentes da série.
- Atualiza componentes conforme série caso turma seja retirada na dispensa em lote.
- Considera serie multi na hora de consultar turma para validações.
- Corrige comando para alterar senha do administrador.

## [2.4.3] - 2021-01-27

Notas completas da versão: [2.4.3](https://github.com/portabilis/i-educar/releases/tag/2.4.3).

### Fixed

- Ajuste do cabeçalho do relatório relação de alunos https://github.com/portabilis/i-educar-reports-package/pull/83.

## [2.4.2] - 2020-12-02

Notas completas da versão: [2.4.2](https://github.com/portabilis/i-educar/releases/tag/2.4.2).

### Changed

- Determina em quais bancos rodar a query.
- Utiliza triggers do Chosen para modificar opções.

### Fixed

- Evita que o campo curso seja limpo ao trocar ano.
- Permite editar empresa.
- Remove validação de ano diferente do ano letivo da escola.

## [2.4.1] - 2020-11-25

Notas completas da versão: [2.4.1](https://github.com/portabilis/i-educar/releases/tag/2.4.1).

### Added

- Implementa validação de permissões de usuários.

### Changed

- Melhora estrutura html dos componentes da turma.
- Melhora criação/exibição dos breadcrumbs do sistema.

### Fixed

- Remove espaços do slug.
- Considera cabeçalho alternativo no calculo de espaço usado do histórico.
- Corrige permissão para tela de usuários.
- Melhora estrutura html dos componentes da turma.
- Corrige como era mostrado a CH na tela de editar servidor.
- Faz redirect se cadastros não existirem.
- Corrige carga horária alocada, quando não possui alocações.

## [2.4.0] - 2020-11-08

Notas completas da versão: [2.4.0](https://github.com/portabilis/i-educar/releases/tag/2.4.0).

### Added

- Adiciona encoding no nome da escola na importação do Educacenso.
- Adiciona campo de data de entrada das matrículas na importação do arquivo do Educacenso.
- Adiciona novos filtros ao relatório de movimento mensal.
- Cria model de benefícios do aluno e relaciona o turno da matrícula.
- Inclui índices em tabelas de faltas, notas e pareceres.

### Changed

- Auditoria centralizada via banco de dados [#687](https://github.com/portabilis/i-educar/pull/687) e [#688](https://github.com/portabilis/i-educar/pull/688).
- Inúmeras melhorias e correções.
- Limpeza e diminuição do código fonte.
- Ajusta importação do endereço da escola.
- Tela para atualização de boletins em lote.
- Funcionalidade para copiar regra de avaliação.
- Aumenta tamanho de campos do sistema.
- Atualizações de campos na tela de configurações do sistema.
- Gera históricos de transferência com a carga horária da série.
- Ajusta nome na tela de detalhe da unificação de pessoas.
- Ajusta nome das áreas de conhecimentos em inputs de seleção.
- Ajusta labels do sistema.
- Ajusta informação de CPF dos responsáveis.
- Refatoração de Histórico escolar.
- Ajusta encoding de strings.
- Trata strings no cadastro de solicitação de transferência.
- Escapa caracteres no nome da pessoa ao setar o contexto usado na auditoria.
- Trata parâmetro código na tela de detalhes da pessoa.
- Migra os dados para a nova auditoria.
- Altera factory de relatório para só pegar os dados caso o relatório use json.

### Removed

- Remove espaços no início e fim do nome da pessoa.
- Remove as triggers da auditoria antiga.
- Remove o uso da auditoria geral.
- Remove tabela antiga de auditoria.
- Remove criação de tabela e triggers antigas de auditoria.
- Remove consultas antigas de auditoria.

### Fixed

- Valida data de saída para liberar CH de servidores.
- Valida se carga horária informada é menor que o total de horas alocadas.
- Corrige a escrita de algumas palavras do sistema.
- Trata strings no cadastro de solicitação de transferência.
- Ajusta informações de datas nos históricos escolares.
- Escapa caracteres no nome da pessoa ao definir o contexto usado na auditoria.
- Trata parâmetro código na tela de detalhes da pessoa.
- Corrige botão de réplica de carga horária e ano letivo.
- Limpa dados do campo Área de conhecimento antes de atualizar os valores.
- Ajusta agrupamento da query de alocações do servidor.
- Adiciona o ato de criação e autorizativo na query padrão dos cabeçalhos.
- Apresenta apenas dados das turmas ativas.
- Corrige validação ao salvar informações de ensino superior de servidores.
- Corrige implementação do sequencial de enturmações na importação.

## [2.3.10] - 2020-08-10

Notas completas da versão: [2.3.10](https://github.com/portabilis/i-educar/releases/tag/2.3.10).

### Added

- Inclui aviso de duplicidade de CNPJ na edição de pessoa jurídica.
- Cria tela de configurações de sistema.
- Novo boletim [parecer descritivo geral](https://github.com/portabilis/i-educar-reports-package/pull/77).

### Changed

- Reestrutura a auditoria do banco de dados.
- Retorna turmas via API que tenham sua regra de avaliação alterada.

### Fixed

- Verifica se existe o ID da pessoa antes de pegar os arquivos.

## [2.3.9] - 2020-08-03

Notas completas da versão: [2.3.9](https://github.com/portabilis/i-educar/releases/tag/2.3.9).

### Added

- Cria novo cadastro de Período de lançamento de notas e faltas por etapa.

### Changed

- Substitui o uso da facade "input" pelo "$request".
- Atualiza Laravel para a versão 6.

### Fixed

- Atualiza data de saída da ultima enturmação quando editado na matrícula.

## [2.3.8] - 2020-07-27

Notas completas da versão: [2.3.8](https://github.com/portabilis/i-educar/releases/tag/2.3.8).

### Added

- Cria componente para inserir arquivos em telas de cadastros.
- Cria comando que gera um CSV a partir de uma query em todas as conexões.

### Changed

- Ajusta parâmetro output do comando query:all-csv.

## [2.3.7] - 2020-07-20

Notas completas da versão: [2.3.7](https://github.com/portabilis/i-educar/releases/tag/2.3.7).

### Added

- Inclui tratamento para trazer o CPF do aluno corretamente na Edição de alunos.
- Adiciona LIMIT para bases com menus duplicados.
- Cria comando para resetar senha do usuário em todas as conexões.

### Changed

- Exibe nome do usuário no histórico de enturmações.
- Concede permissão para a tela de consulta do censo a todos os usuários que já tem acesso a exportação.
- Ajusta nome de tabela usada em migrations.

### Fixed

- Limita caracteres do campo "Abreviatura" na tela de cadastro de função do servidor.
- Corrige rollback de migration que altera coluna.

## [2.3.6] - 2020-07-13

Notas completas da versão: [2.3.6](https://github.com/portabilis/i-educar/releases/tag/2.3.6).

### Added

- Cria configuração na tabela de settings para obrigar ou não o preenchimento do campo Cartão do SUS no cadastro de pessoa e de aluno.
- Cria novo tipo de exportação de dados para etapas.
- Cria parâmetro arquivo no comando query:all.

### Changed

- Exporta campo Conveniada com poder público sempre como "Null".
- Ajusta update de campos numéricos.

### Fixed

- Corrige endereço da escola na exportação do Educacenso.
- Considera escola e ano ao pegar as series para o campo multisseriada no cadastro de turmas.

## [2.3.5] - 2020-07-06

Notas completas da versão: [2.3.5](https://github.com/portabilis/i-educar/releases/tag/2.3.5).

### Added

- Inclui método para tratar caracteres especiais em várias telas do i-Educar.
- Cria parâmetro no query:all para não executar em determinados bancos.

### Changed

- Tela de consulta dos dados exportados para o Educacenso.
- Abre notificações em uma nova aba.
- Exibe código HTML nas notificações.

### Removed

- Remove campos antigos da tabela de servidor.

### Fixed

- Corrige Breadcrumb na tela da agenda.
- Corrige bug na função que trata datas em ano bissexto.

## [2.3.4] - 2020-06-29

Notas completas da versão: [2.3.4](https://github.com/portabilis/i-educar/releases/tag/2.3.4).

### Added

- Permite alterar a etapa do aluno na turma também em enturmações inativas.
- Implementa Checkbox em deficiência, no cadastro do aluno, para obrigar ou não o laudo médico.
- Adiciona novo histórico escolar https://github.com/portabilis/i-educar-reports-package/pull/75.
- Implementa validação para ano bissexto ao enturmar alunos.

### Changed

- Adéqua importação de 2019 para as mudanças de dados de 2020.
- Ajustes na importação do Educacenso de 2020.
- Ajusta regras do campo de recursos para a prova INEP do registro 30 do Educacenso.
- Trata o array de deficiências na exportação do Educacenso.
- Desconsidera Altas habilidades na validação das deficiências.
- Não reporta exceção de regra de negócio.
- Pega configurações do ReCAPTCHA no `.env` [#714](https://github.com/portabilis/i-educar/pull/714) .

### Removed

- Remove a deficiência Altas habilidades/Super dotação somente na validação dos recursos.
- Remove campos não utilizados da tabela de turmas.
- Remove campos não utilizados no cadastro de Escola.

### Fixed

- Valida ano do arquivo na hora da importação.
- Melhora a validação dos campos de transporte no cadastro de aluno.

## [2.3.3] - 2020-06-22

Notas completas da versão: [2.3.3](https://github.com/portabilis/i-educar/releases/tag/2.3.3).

### Added

- Cria configuração para transformar o nome da pessoa em caixa alta.
- Adiciona verificação para o caso de requisição sem usuário logado.
- Adiciona reCAPTCHA v3 ao formulário de login.

### Changed

- Melhorias na tela de atualização da data de entrada e enturmação em lote.
- Ajusta validação de choque de horário de matrícula para somente considerar turmas informadas ao Educacenso.
- Obriga e valida preenchimento do campo órgãos colegiados da escola.
- Ajusta método que verifica se o usuário logado é admin.

### Fixed

- Corrige campo de veículo de transporte.
- Melhora validação de transporte escolar no cadastro do aluno.

## [2.3.2] - 2020-06-17

Notas completas da versão: [2.3.2](https://github.com/portabilis/i-educar/releases/tag/2.3.2).

### Added

- Cria botão para copiar dados dos componentes na tela de séries da escola.
- Cria importador de usuários no i-Educar.
- Cria tela para atualizar a data de entrada e enturmação de matrículas em lote.
- Adiciona novos ícones.

### Changed

- Ajustes em regras de avaliação e faltas e notas para o regime cíclico.
- Trata campo Banheiros direto no arquivo de Exportação do Censo Escolar.
- Altera o `UrlPresigner` para permitir uso local usando `filesystems.default` [#711](https://github.com/portabilis/i-educar/pull/711).

### Fixed

- Remove redirecionamento quando não tem permissão para exclusão.
- Ajusta comportamento ao salvar campo `force_reset_password`.

## [2.3.1] - 2020-06-08

Notas completas da versão: [2.3.1](https://github.com/portabilis/i-educar/releases/tag/2.3.1).

### Added

- Cria comando para alteração em lote de componentes curriculares.
- Cria endpoint para retornar os componentes migrados.
- Implementa opção de selecionar os services que serão usados ao copiar dados dos componentes.
- Cria parâmetro para mostrar apenas cursos ativos.
- Adiciona opção 'Não informado' para estado civil.
- Implementa campo 'Descrição' na tela de detalhes da matrícula.
- Cria novo tipo de exportação para dados relacionados a assistência social.
- Permite girar a foto do aluno.
- Cria tela para exibir log da unificação de pessoas.
- Incluir campo de Recursos tecnológicos que o aluno possui ao cadastro do aluno.
- Inclui busca por CPF e RG na tela de listagem de alunos.
- Habilita exportação do Censo Escolar 2020.
- Implementa debug no serviço de migração de dados de componentes.
- Permite desabilitar o sistema de notificações em tempo real.
- Adiciona tags aos jobs para tornar mais fácil a identificação no Horizon.
- Adiciona tags nas notificações para facilitar a identificação no Horizon.
- Cria função pra evitar problemas com data em ano bissexto.
- Adiciona método para faltas gerais.
- Cria slug ao fazer insert de pessoa pelo model.

### Changed

- Exportador de dados em arquivo CSV.
- Atualiza opções de atividades complementares.
- Envia notificação para ajuste de configurações na instituição.
- Altera importação do censo para executar uma escola de cada vez.
- Atualizações da tabela de Instituições de Ensino Superior.
- Exportação Registro 10 Educacenso 2020.
- Altera a data de referência do Educacenso para 11/03/2020.
- Ajusta regra dos campos 110 à 112 do registro 10.
- Ajusta condição do campo Recebe Escolarização na exportação do registro 60.
- Atualiza dados de curso superior de acordo com tabela de 2020 do Educacenso.
- Altera API para sempre retornar as turmas mesmo sem regra.
- Altera o `UrlPresigner` para permitir uso local.
- Altera tipo de campo de Média e Média recuperação de `numeric` para `decimal`.
- Atualiza versão do jQuery form.

### Removed

- Remove validação que obriga CPF para alunos de EJA ou Educação Profissional.
- Remove tabela de etapa Educacenso da turma.

### Fixed

- Validar se existem lançamentos no diário ao remover componentes no i-Educar.
- Corrige mensagem de aviso na analise do exportador Educacenso.
- Valida cursos de formação superior dos servidores para o Educacenso 2020.
- Altera query que ordena alunos da turma pra considerar também o sequencial.
- Pega somente unificações do tipo certo na tela de unificações de alunos.
- Altera código do processo de menus duplicados.
- Altera coluna tipo_vinculo em `professor_turma_excluidos` para nullable.
- Altera tipo do campo texto da notificação de `varchar` para `text`.
- Ajusta validação de telefones da escola, caso nenhum telefone seja informado.
- Adiciona query string com os filtros aos links da paginação.
- Ajusta processamento do histórico.
- Utiliza o arredondamento de média.
- Verifica se usa cálculo de média de recuperação existe.
- Corrige erro ao calcular média das notas.
- Ajusta bug ao listar turmas quando o usuário logado é um professor.
- Melhora consultas de busca de alunos.
- Ajusta validação de lançamentos.
- Corrige bug no tratamento de exibição do campo exame final.
- Ajusta verificação de reprovação automática.
- Corrige Breadcrumbs e títulos internos no módulo "Servidores > Cadastro".
- Corrige bug na alteração de funções do servidor.

## [2.3.0] - 2020-06-05

Notas completas da versão: [2.3.0](https://github.com/portabilis/i-educar/releases/tag/2.3.0).

### Added

- Adiciona suporte ao PHP 7.4.
- Adiciona turno da turma na importação do censo e cria comando para atualizar turmas existentes.
- Criar exportador de dados para o SEB.
- Adiciona dados do controller e action ao log do HoneyBadger.
- Adiciona dados para log dos erros em relatórios.
- Permite alterações em usuários do tipo `admin` apenas por outros usuários do mesmo nível de permissão.
- Adiciona colunas de geolocalização.
- Adiciona schema nas migrations do schema public.

### Changed

- Simplificação do módulo de endereçamento.
- Exportador de dados para o SEB.
- Consolidação das migrations banco de dados.
- Limpeza e diminuição do código fonte.
- Melhora a imagem PHP do Docker.
- Força parâmetro etapa ao rodar a promoção.
- Code style.

### Removed

- Remove suporte ao PHP 7.2.
- Remove ferramentas de unificação de endereço.
- Remove antigas páginas do módulo de endereçamento.

### Fixed

- Corrige problemas em relatórios decorrentes do módulo de endereço.
- Inverte condição para marcar como remanejado apenas depois da data base.
- Permite cadastrar a mesma sigla para países diferentes.
- Corrige página que salva histórico escolar.
- Ajusta o módulo de endereçamento da unificação de pessoas.
- Considera apenas enturmações ativas para checar dados de horário.
- Corrige unificação de pessoas.
- Corrige campo search distrito.
- Exibe apenas estados do Brasil.
- Corrige a busca de editoras.
- Roda a atualização de média na falta, para atualizar a situação do componente.
- Considera na view de situação apenas última enturmação da matrícula na turma.
- Corrige problema com cadastro de novos tipos de usuário.
- Ajusta log de erros no console.
- Reordena sequenciais de acordo com a data base.
- Formata CEP.
- Considera ano da alocação na soma das atribuições.
- Corrige encode do arquivo.
- Corrige bug em caracteres não escapados.
- Permite apagar turno do vínculo do professor com a turma.
- Corrige bug ao criar alunos estrangeiros no cadastro simplificado de aluno.
- Corrige validação do nome social.

## [2.2.22-upgrade] - 2020-06-17 [PRE-RELEASE]

Notas completas da versão: [2.2.22-upgrade](https://github.com/portabilis/i-educar/releases/tag/2.2.22-upgrade).

### Fixed

- Corrige o nome das migrations ao efetuar a migração.

## [2.2.21-upgrade] - 2020-06-03 [PRE-RELEASE]

Notas completas da versão: [2.2.21-upgrade](https://github.com/portabilis/i-educar/releases/tag/2.2.21-upgrade).

### Added

- Adiciona suporte ao PHP 7.4.
- Adiciona turno da turma na importação do censo e cria comando para atualizar turmas existentes.
- Criar exportador de dados para o SEB.
- Adiciona dados do controller e action ao log do HoneyBadger.
- Adiciona dados para log dos erros em relatórios.
- Permite alterações em usuários do tipo `admin` apenas por outros usuários do mesmo nível de permissão.
- Adiciona colunas de geolocalização.
- Adiciona schema nas migrations do schema public.

### Changed

- Simplificação do módulo de endereçamento.
- Exportador de dados para o SEB.
- Consolidação das migrations banco de dados.
- Limpeza e diminuição do código fonte.
- Melhora a imagem PHP do Docker.
- Força parâmetro etapa ao rodar a promoção.
- Code style.

### Removed

- Remove suporte ao PHP 7.2.
- Remove ferramentas de unificação de endereço.
- Remove antigas páginas do módulo de endereçamento.

### Fixed

- Corrige problemas em relatórios decorrentes do módulo de endereço.
- Inverte condição para marcar como remanejado apenas depois da data base.
- Permite cadastrar a mesma sigla para países diferentes.
- Corrige página que salva histórico escolar.
- Ajusta o módulo de endereçamento da unificação de pessoas.
- Considera apenas enturmações ativas para checar dados de horário.
- Corrige unificação de pessoas.
- Corrige campo search distrito.
- Exibe apenas estados do Brasil.
- Corrige a busca de editoras.
- Roda a atualização de média na falta, para atualizar a situação do componente.
- Considera na view de situação apenas última enturmação da matrícula na turma.
- Corrige problema com cadastro de novos tipos de usuário.
- Ajusta log de erros no console.
- Reordena sequenciais de acordo com a data base.
- Formata CEP.
- Considera ano da alocação na soma das atribuições.
- Corrige encode do arquivo.
- Corrige bug em caracteres não escapados.
- Permite apagar turno do vínculo do professor com a turma.
- Corrige bug ao criar alunos estrangeiros no cadastro simplificado de aluno.
- Corrige validação do nome social.

## [2.2.20] - 2020-03-02

Notas completas da versão: [2.2.20](https://github.com/portabilis/i-educar/releases/tag/2.2.20).

### Added

- Adiciona namespace dos Reports no composer.json.

### Changed

- Desabilita temporariamente o lançamento de faltas para casos em que a regra não usa nota.
- Migra e adiciona primária na tabela configuracoes_gerais.
- Code style.

### Removed

- Remove usos da tabela endereco_externo.

### Fixed

- Salva campo data de expiração corretamente.
- Trata campos opcionais na importação do registro 10.
- Utiliza ano letivo do formulário.
- Passa etapas no lançamento de faltas onde a etapa já foi concluída.
- Altera verificação de situação Aprovado com dependência.

## [2.2.19] - 2020-02-11

Notas completas da versão: [2.2.19](https://github.com/portabilis/i-educar/releases/tag/2.2.19).

### Added

- Adiciona transação no processo de solicitação de transferência.
- Adiciona coluna para armazenar o distrito na escola.
- Cria função para retornar o telefone da pessoa.

### Changed

- Torna obrigatório informar pelo menos um telefone para pessoa física.
- Pega somente matrículas de alunos ativos ao montar a tela do faltas e notas.
- Altera `legacy.php` para aceitar `env('REPORTS_LOGO')`.
- Refatora tela de histórico de enturmações.
- Assets.
- Models.
- SourceLevel.
- Code style.

### Removed

- Remove todas as ocorrências de `endereco_externo`.

### Fixed

- Remove limit da query que lista os CEPs.
- Ajusta faltas e notas para não definir situação como Retido em regra com progressão continuada.
- Não valida se existem lançamentos no i-Diário se não houver token.
- Valida série e ano letivo antes de rodar a promoção.
- Trata disciplinas aglutinadas na aprovação por dependência.
- Ajusta condição de busca por matrícula.

## [2.2.18] - 2020-01-20

Notas completas da versão: [2.2.18](https://github.com/portabilis/i-educar/releases/tag/2.2.18).

### Added

- Adiciona CPF ao retorno dos dados do candidato.
- Adiciona validação ao excluir.

### Changed

- Importador do Educacenso 2019.
- Torna fixo os anos disponíveis para importação do censo.
- Ajustes na importação do censo 2019.
- Exibe Plus(+) mais nota máxima.
- Aplica padrão de 1 casa decimal para percentual de frequência.
- Arredonda média para quantidade de casas decimais permitidas.
- Ajusta remanejamento e desenturmação para reordenar os sequenciais da turma.
- Retorna todos anos letivos das escolas.
- Ajustes no Educacenso após testes.
- Macro para consulta sem acentos.
- Models.
- Code style.

### Removed

- Remove diversos helpers HTML não utilizados.
- Remove cadastro de setores.
- Remove o uso de clsPmieducarEscolaComplemento.
- Remove testes de páginas que serão excluídas.

### Fixed

- Valida enturmação ao cancelar solicitação de transferência.
- Exibe alunos com dependência quando filtrado por componente.
- Ajusta queries do movimento mensal para considerar como matrícula inicial alunos que entraram na data base.
- Valida uso da dependência no faltas e notas para setar a situação final
- Ajusta promoção para lançar faltas também para casos em que a regra não usa nota.
- Corrige bug no select de anos.
- Altera service boletim para desconsiderar lançamentos em componentes que não são da matrícula.
- Ajusta validação de nota máxima no endpoint do envio de notas.
- Ajusta correção de acentuação.
- Corrige a busca do número de dependências de regra diferenciada.
- Verificar se a matricula anterior é de transferencia.
- Corrige erro ao buscar arquivo bg.svg.
- Corrige testes devido a ano fixo [#695](https://github.com/portabilis/i-educar/pull/695).

## [2.2.17] - 2019-12-26

Notas completas da versão: [2.2.17](https://github.com/portabilis/i-educar/releases/tag/2.2.17).

### Changed

- Padroniza endereçamento de Pessoas Físicas e Jurídicas.
- Ajusta função para ao atualizar a média de um componente, atualize sempre a média dos outros componentes aglutinados mesmo que setado no serviceBoletim apenas um componente.

### Removed

- Remove not null da coluna `formula_media_id` de `modules.regra_avaliacao`.

### Fixed

- Ajusta calculo de média para disciplinas aglutinadas.
- Ajusta carga horária para views de histórico escolar.
- Corrige nome da classe.

## [2.2.16] - 2019-12-18

Notas completas da versão: [2.2.16](https://github.com/portabilis/i-educar/releases/tag/2.2.16).

### Added

- Cria parâmetro para aglutinação médias de disciplinas para calculo de situação.
- Cria tela para atualizar a situação de matrículas em massa.
- Adiciona ternário para data nascimento.
- Adiciona mensagens de erros conhecidas para o i-Diário.

### Changed

- Ajusta atualização de pessoas em modais.
- Ajusta endpoint de disciplinas do servidor.
- Busca código INEP do município e distrito de endereço externo ao exportar para o censo.
- Style code.

### Removed

- Remove configuração default de certificados.

### Fixed

- Valida dispensa de disciplina na matricula.
- Trata caso em que nenhuma data é encontrada.
- Desconsidera disciplina dispensada da turma antes de calcular situação da falta.
- Trata método que busca matrículas no faltas e notas para retornar somente ativas.
- Corrige bug ao pegar etapas para casos de etapas específicas por componente.
- Trata casos em que a disciplina não é encontrada no cálculo de disciplinas aglutinadas.
- Corrige breadcrumb detalhes da regra de avaliação.

## [2.2.15] - 2019-12-02

Notas completas da versão: [2.2.15](https://github.com/portabilis/i-educar/releases/tag/2.2.15).

### Changed

- Melhora a performance da postagem de notas.
- Melhoria nas mensagens de rematrícula automática.
- Mantem valor no filtro depois da pesquisa.
- Inserir novo valor na tabela estado civil.
- Altera gravação/visualização de arquivos na S3.
- Pula testes afetados pela implementação de cache.
- Refatora cópia de dados de matrículas transferidas.
- Style code.

### Fixed

- Validação na exclusão de escolaridade.
- Utiliza versão correta do Java na imagem PHP [#682](https://github.com/portabilis/i-educar/pull/682).
- Adiciona coluna esquecida  [#685](https://github.com/portabilis/i-educar/pull/685).
- Corrige página detalhe do vínculo escola e série.
- Corrige erro ao cadastrar editora sem endereço.
- Reverte identificação de usuário na auditoria de notas.
- Exibe número NIS mesmo iniciando com zeros a esquerda.
- Exibe a carga horária correta.
- Adiciona condição para remover cursos e disciplina.
- Corrige suporta a string com aspas.
- Altera acentuação na mensagem de aviso.
- Não permite seleção de disciplinas dispensadas no vinculo de professores com turma.
- Trata casos de urls de fotos com parâmetros.
- Trata casos em que a URL da foto é inválida.
- Corrige método que monta URL para foto no S3.
- Trata strings da listagem.

## [2.2.14] - 2019-11-20

Notas completas da versão: [2.2.14](https://github.com/portabilis/i-educar/releases/tag/2.2.14).

### Added

- Adiciona auditoria de notas ao lançar dispensa.
- Adiciona extensão pcntl a imagem PHP [#678](https://github.com/portabilis/i-educar/pull/678).

### Changed

- Busca apenas séries vinculadas aos cursos da escola.
- Altera a fábrica de conexão com o banco de dados.
- Utiliza versão correta do Java na imagem PHP [#682](https://github.com/portabilis/i-educar/pull/682).
- Style code.

### Removed

- Remove arquivo educar_aluno_cad_xml.php.

## [2.2.13] - 2019-11-12

Notas completas da versão: [2.2.13](https://github.com/portabilis/i-educar/releases/tag/2.2.13).

### Added

- Adiciona opção de tipo sanguíneo no multipleSearch.
- Adiciona extensão pcntl a imagem PHP [#678](https://github.com/portabilis/i-educar/pull/678).

### Changed

- Copia campo de local diferenciado da turma para turma do novo ano letivo.
- Customiza páginas de erro.
- Carrega configurações para as rotas da API.
- Ajusta listagem de backups para fazer download de URLs assinadas.
- Loga o payload e response ao gerar relatório.
- Ajusta helper para campo hora aceitar também segundos.
- Models para interagir com o banco de dados.
- Style code.

### Fixed

- Corrige bug ao identificar a disciplina dispensada da turma.

## [2.2.12] - 2019-11-04

Notas completas da versão: [2.2.12](https://github.com/portabilis/i-educar/releases/tag/2.2.12).

### Added

- Adiciona o pacote [Laravel Plug and Play](https://github.com/edersoares/laravel-plug-and-play).
- Insere permissão para reclassificação de matrícula.

### Changed

- Passa parâmetro de ano para validar lançamentos.
- Ajusta validação de nota máxima e mínima.
- Loga erros da requisição ao servidor de relatórios.
- Models para interagir com o banco de dados.
- Style code.

### Fixed

- Corrige tela de calendário letivo.

## [2.2.11] - 2019-10-29

Notas completas da versão: [2.2.11](https://github.com/portabilis/i-educar/releases/tag/2.2.11).

### Added

- Cria parâmetro para configurar a ordenação dos alunos.

### Changed

- Recalculas as médias na dispensa de componentes para o aluno.
- Style code.

### Removed

- Remove referências a tabela fisica_cpf.

### Fixed

- Considera apenas último sequencial na turma ao lançar nota.
- Corrige bug que não permitia imprimir agenda [#667](https://github.com/portabilis/i-educar/pull/667).
- Correções de PHP Docs, PSR-12 e retirados uses não utilizados [#671](https://github.com/portabilis/i-educar/pull/671).

## [2.2.10] - 2019-10-21

Notas completas da versão: [2.2.10](https://github.com/portabilis/i-educar/releases/tag/2.2.10).

### Added

- Adiciona os campos latitude e longitude no cadastro de escola.

### Changed

- Colore situação diferenciada da matrícula no lançamento de notas.
- Atualiza nome do curso superior de acordo com a tabela do Educacenso.

### Fixed

- Desconsidera disciplina dispensada da turma.

## [2.2.9] - 2019-10-14

Notas completas da versão: [2.2.9](https://github.com/portabilis/i-educar/releases/tag/2.2.9).

### Changed

- Altera a `view_situacao`.
- Ajusta `group by` da consulta de professores por turma da escola.
- Style code #664.

### Fixed

- Corrige bug na aprovação de componentes com dispensa.
- Corrige problema com matrículas de usuário na unificação de pessoa.
- Corrige comportamento do check de etapas específicas na tela de séries da escola.

## [2.2.8] - 2019-10-08

Notas completas da versão: [2.2.8](https://github.com/portabilis/i-educar/releases/tag/2.2.8).

### Changed

- Altera o host do banco de dados ao fazer build [#665](https://github.com/portabilis/i-educar/pull/665).

### Removed

- Remove espaços no campo RG.
- Remove CSS de localização do sistema.

### Fixed

- Corrige regras de ordenação.
- Corrige funcionamento do endpoint alunos-matriculados.
- Habilita campo anos letivos ao clicar para selecionar todos.

## [2.2.7] - 2019-09-30

Notas completas da versão: [2.2.7](https://github.com/portabilis/i-educar/releases/tag/2.2.7).

### Added

- Adiciona saia como opção do kit de uniforme escolar.
- Permite utilizar o instalador para fazer o upgrade de versão [#659](https://github.com/portabilis/i-educar/pull/659).
- Cria testes para os cenários de dependência e dispensa de componentes.
- Cria teste de progressão de média ponderada.

### Changed

- Modifica implementação de `DataMapper` a fim de evitar falta de performance.
- Trait para incrementar sequence.
- Não loga as queries no Telescope duas vezes.
- Trocado o nome da tabela `matricula` por `matricula_turma` em boletim https://github.com/portabilis/i-educar-reports-package/pull/69.

### Removed

- Remove bloqueio de matrículas fora do calendário letivo.
- Remove adição do CSS de localização do sistema.
- Remove arquivos referenciados apenas entre eles.

### Fixed

- Corrige bug na tela de ocorrências disciplinares.
- Corrige o slug da área pública.
- Corrige problema de redirecionamento em página que utilizam filtros.
- Corrigido o título de alguns menus https://github.com/portabilis/i-educar-reports-package/pull/68.

## [2.2.6] - 2019-09-23

Notas completas da versão: [2.2.6](https://github.com/portabilis/i-educar/releases/tag/2.2.6).

### Added

- Cria tela de consulta de dispensas.

### Changed

- Aumenta a descrição do valor da tabela de arredondamento.
- Altera exibição de mensagens no faltas e notas.
- Torna global middleware de conexão ao banco de dados.
- Mostra detalhes das dispensas ao remover um componente na tela de séries da escola.
- Melhoria de código.
- Comando para executar queries em todas as conexões.
- Ajusta badges para utilizar build do Travis  [#656](https://github.com/portabilis/i-educar/pull/656).
- Endpoint para retornar a versão do i-Educar [#657](https://github.com/portabilis/i-educar/pull/657).

### Removed

- Remove verificações antigas que não necessárias.
- Remove páginas antigas de alunos.
- Remove código comentado que prejudica a refatoração.

## [2.2.5] - 2019-09-16

Notas completas da versão: [2.2.5](https://github.com/portabilis/i-educar/releases/tag/2.2.5).

### Changed

- Executa alterações de componentes somente após a validação na tela de séries da escola.
- Ajustes no importador de componentes curriculares.
- Ajusta mascara do código RA.
- Ajusta limitação de filtros para usuários que são "apenas" professores.
- Teste unitário de progressão continuada.
- Melhoria de código no módulo endereçamento.
- Chave primária em escola_serie_disciplina.

### Removed

- Remove arquivos PHP de download não utilizados.
- Remove mensagens inúteis.
- Remove antigo copyrights.

## [2.2.4] - 2019-09-10

Notas completas da versão: [2.2.4](https://github.com/portabilis/i-educar/releases/tag/2.2.4).

### Added

- Implementa um exemplo de teste em DiarioApi.
- Cria helper de campo escola obrigatório para usuários nível escolar.
- Adiciona type hint nas factories.

### Changed

- Bloqueia alteração do turno do vínculo do professor com a turma caso haja algum lançamento no i-Diário.
- Ajustes para adicionar variáveis a view.
- Torna o laudo obrigatório caso exista deficiência.
- Ajusta comando que altera série da turma.
- Links de suporte da comunidade [#650](https://github.com/portabilis/i-educar/pull/650) e [#651](https://github.com/portabilis/i-educar/pull/651).
- Testes referentes ao cenário de Progressão por média e presença sem recuperação.
- Remoção de métodos não utilizados em diversas classes.
- Melhoria de código.
- Melhora models para substituir queries em relatórios.

### Removed

- Remove arquivos não utilizados.
- Remove a inclusão duplicada do CSS de breadcrumb.
- Remove código JavaScript não utilizado.

## [2.2.3] - 2019-09-02

Notas completas da versão: [2.2.3](https://github.com/portabilis/i-educar/releases/tag/2.2.3).

### Added

- Cria endpoint para retornar as unificações de alunos.
- Adiciona custom labels para os nomes dos modelos de boletim do professor.
- Adiciona campo de escolaridade no método de cadastro.

### Changed

- Altera endpoint de unficações de alunos para receber várias escolas.
- Altera valor default da configuração `nao_apresentar_campo_alfabetizado`.
- Busca apenas séries ativas.
- Ajusta ordenação de escolas na tela de solicitar transferência.
- Melhorias de código.
- Move chamada de metodos de remanejamento para o controller.

### Fixed

- Aprova aluno quando possui dispensa geral.
- Corrige itens dos multi-selects quando não há opções.
- Ajusta código que exclui funções do servidor.
- Corrige bug ao matricular aluno vindo de transferência.
- Corrige view_dados_escola.
- Corrige endpoint da busca da rápida de menus.
- Adiciona validação para exibir modal.
- Altera validação de nota máxima de recuperação para pegar o valor correto.
- Corrige assinatura do método tearDown() dos testes legados.

## [2.2.2] - 2019-08-22

Notas completas da versão: [2.2.2](https://github.com/portabilis/i-educar/releases/tag/2.2.2).

### Changed

- Endpoint para buscar parâmetros da escola
- Funcionalidade para reordenar matrículas de acordo com a data base
- Altera execução de queries legadas do i-Educar para usar a conexão default do Laravel
- PSR-2 em diversos arquivos
- Melhorias de performance nas APIs de lançamento de notas

### Fixed

- Ajusta bug na validação de choque de horário na matrícula
- Corrige problema na exibição da situação do aluno na tela de faltas e notas
- Corrige bug na tela de cadastro de CEP
- Ajusta redirecionamentos no cadastro de função
- Corrige problema ao salvar tabela de arredondamento

## [2.2.1] - 2019-08-12

Notas completas da versão: [2.2.1](https://github.com/portabilis/i-educar/releases/tag/2.2.1).

### Changed

- Endpoint para retornar regras da série.
- Serviço para reordenar enturmações.
- PSR-2.
- Extende modelos legado a uma classe comum: `iEducar\Legacy\Model`.
- Altera título dos menus de atestado/declaração.
- Atualiza OpenJDK no Dockerfile [#643](https://github.com/portabilis/i-educar/pull/643).

### Removed

- Remove hash de verificação do Composer no Dockerfile [#644](https://github.com/portabilis/i-educar/pull/644).

### Fixed

- Corrige query no endpoint de alunos.
- Valida se usuário não expirou ao fazer login.
- Restaura ícone para fechar modais.
- Remove redirect infinito.
- Adiciona schemas nas funções de frequência.

## [2.2.0] - 2019-08-05

Notas completas da versão: [2.2.0](https://github.com/portabilis/i-educar/releases/tag/2.2.0).

### Changed

- Atualização do PHP.
- Atualização do Laravel.
- Reestruturação do banco de dados.
- Limpeza e diminuição do código fonte.
- Atualiza a versão do PHP para 7.3 [#586](https://github.com/portabilis/i-educar/pull/586).
- Atualiza o Laravel para a versão 5.8 [#585](https://github.com/portabilis/i-educar/pull/585).
- Limpa o schema `public` #516.
  - Remove funções e triggers não utilizadas.
  - Remove algumas tabelas desnecessárias.
- Converte migrations de insert em seeders [#559](https://github.com/portabilis/i-educar/pull/559).
- Utiliza seeders ao invés de migrations de inserts [#567](https://github.com/portabilis/i-educar/pull/567).
- Melhora implementação das classes bases #513.

### Removed

- Remove o schema `alimentos` #496.
- Remove o schema `consistenciacao` #497.
- Remove o schema `conv_functions` #498.
- Remove o schema `serieciasc` #499.
- Remove o schema `pmiacoes` #500.
- Remove o schema `pmidrh` #501.
  - Há efeito colateral, ver pull request #501 e issue #502.
- Remove o schema `pmiotopic` #503.
- Remove o schema `historico` #507.
- Remove o schema `acesso` #576.
- Remove o schema `pmicontrolesis` #606.
  - Inicialmente o PR #508 mantinha as tabelas tutormenu, menu, submenu_portal e menu_portal.
- Remove tabelas, view e functions do schema `cadastro` #610.
- Remove a tabela `historico_educar` #601.
- Remove tabelas do schema `portal` #509.
  - Diversas tabelas foram removidas, ver pull request #509.
- Remove seeder da tabela `portal.acesso` #602.
- Remove tabelas de menus do schema `portal` #607.
- Remove tabelas `imagem` e `imagem_tipo` #608.
- Remove tabela `intranet_segur_permissao_negada` #609.
- Remove a tabela `changelog` #504.
- Remove a tabela `phinxlog` #505.
- Remove a tabela `portal_banner` #514
- Remove extensão `pg_stat_statements` #515.
- Remove constantes legadas definidas no arquivo `ieducar/intranet/includes/bootstrap.php` #533.
  - CORE_EXT_CONFIGURATION_ENV
  - DS
  - PROJECT_ROOT
  - APP_ROOT
- Remove arquivos não referenciados #510.
- Remove arquivos utilizados em testes antigos #512.
- Remove  `ieducar/intranet/includes/bootstrap.php` #534 #535.
- Remove `ieducar/vendor/autoload.php` #536.
- Remove a pasta `ieducar/configuration` #537.
- Remove classes e testes sobre `.ini` #537.
- Remove splashscreen e imagens não utilizadas #544.
- Remove fontes e imagens não utilizadas #545.
- Remove arquivos API XML #546.
- Remove classe clsConfig #547.

## [2.1.25-upgrade] - 2019-09-24 [PRE-RELEASE]

Notas completas da versão: [2.1.25-upgrade](https://github.com/portabilis/i-educar/releases/tag/2.1.25-upgrade).

### Added

- Permite utilizar o instalador para fazer o upgrade de versão.

### Changed

- Atualização do PHP.
- Atualização do Laravel.
- Reestruturação do banco de dados.
- Limpeza e diminuição do código fonte.

### Fixed

- Corrige problema ao rodar o upgrade devido a coluna `cartorio_cert_civil` da tabela `cadastro.documento` possuir mais de 190 caracteres.

## [2.1.24-upgrade] - 2019-09-18 [PRE-RELEASE]

Notas completas da versão: [2.1.24-upgrade](https://github.com/portabilis/i-educar/releases/tag/2.1.24-upgrade).

### Changed

- Atualização do PHP.
- Atualização do Laravel.
- Reestruturação do banco de dados.
- Limpeza e diminuição do código fonte.

### Fixed

- Corrige problema ao rodar o upgrade devido a coluna `cartorio_cert_civil` da tabela `cadastro.documento` possuir mais de 190 caracteres.

## [2.1.23-upgrade] - 2019-08-05 [PRE-RELEASE]

Notas completas da versão: [2.1.23-upgrade](https://github.com/portabilis/i-educar/releases/tag/2.1.23-upgrade).

### Changed

- Atualização do PHP.
- Atualização do Laravel.
- Reestruturação do banco de dados.
- Limpeza e diminuição do código fonte.

### Fixed

- Corrige problema ao rodar o upgrade devido a coluna `cartorio_cert_civil` da tabela `cadastro.documento` possuir mais de 190 caracteres.

## [2.1.22-upgrade] - 2019-07-29 [PRE-RELEASE]

Notas completas da versão: [2.1.22-upgrade](https://github.com/portabilis/i-educar/releases/tag/2.1.22-upgrade).

### Changed

- Atualização do PHP.
- Atualização do Laravel.
- Reestruturação do banco de dados.
- Limpeza e diminuição do código fonte.

## [2.1.21] - 2019-07-29

Notas completas da versão: [2.1.21](https://github.com/portabilis/i-educar/releases/tag/2.1.21).

### Added

- Adiciona valor default da situação para o boletim.
- Cria comando para importar as séries da escola.
- Adiciona parâmetro etapa ao endpoint de boletim.

### Changed

- Armazena arquivo de saída o importador de componentes no servidor local.
- Cálculos para definir nota de etapa quando utiliza recuperação paralela.

### Fixed

- Valida data de saída da enturmação.
- Corrige campo ambíguo em consulta SQL.
- Converte campos em array antes de tratar valores.

## [2.1.20] - 2019-07-22

Notas completas da versão: [2.1.20](https://github.com/portabilis/i-educar/releases/tag/2.1.20).

### Added

- Cria validação para que não seja possível vincular um professor na turma se o mesmo não estiver alocado na escola.
- Adiciona `calcula_media_rec_paralela` na Api de regras de avaliações.

### Fixed

- Corrige bug ao pegar regra de avaliação na função `modules.frequencia_da_matricula`.
- Bloqueia o acesso de usuários inativos.
- Pega somente turmas ativas na exportação do registro 60.

## [2.1.19] - 2019-07-15

Notas completas da versão: [2.1.19](https://github.com/portabilis/i-educar/releases/tag/2.1.19).

### Changed

- Storage feature (https://github.com/portabilis/i-educar/pull/618).
- Ordena listagem de escolas alfabeticamente.
- Calcula a média ao utilizar recuperação paralela.
- Considera boletim diferenciado nas turmas criadas de novos anos letivos.
- Preenche automaticamente etapas da turma quando não segue o padrão ano escolar.
- Models e factories.
- Altera enviroment usado para log no honeybadger.

### Fixed

- Valida se possui boletim diferenciado para a turma.
- Correção da query de buscar endereço pois esta buscando distrito errado (https://github.com/portabilis/i-educar/pull/612).
- Remove verificação de permissão da agenda.

## [2.1.18] - 2019-07-05

Notas completas da versão: [2.1.18](https://github.com/portabilis/i-educar/releases/tag/2.1.18).

### Added

- Adiciona validação ao inativar turma com matriculas.

### Changed

- Atualiza endpoints da API para melhor sincronização com i-Diário.
- Executa scripts de tratamentos de dados para o Educacenso 2019 após a importação do arquivo.
- Migra configurações do arquivo .ini para o banco de dados.

### Removed

- Remove type hint de método que seta médias gerais.

### Fixed

- Corrige modal da etapas da turma.
- Corrige casos de escolas sem gestor definido.
- Corrige busca pelo período de enturmação.
- Corrige botão de cancelamento no cadastro da turma.
- Corrige campo de justificativa para apagar corretamente informação.
- Ajusta regra de remanejamento na enturmação.

## [2.1.17] - 2019-06-25

Notas completas da versão: [2.1.17](https://github.com/portabilis/i-educar/releases/tag/2.1.17).

### Changed

- Melhora telas de cadastro de tipos de usuário.
- Melhora as validações da tela de enturmação de alunos.
- Exibe o motivo de não ser possível excluir uma pessoa.

### Removed

- Remove classes de repositórios não utilizadas.

### Fixed

- Remove componentes duplicados ao exportar o registro 50 do Educacenso.
- Adiciona tratamento para caso em que não encontrar datas das etapas da turma.
- Corrige tela de cadastro de uma nova escola.

## [2.1.16] - 2019-06-17

Notas completas da versão: [2.1.16](https://github.com/portabilis/i-educar/releases/tag/2.1.16).

### Added

- Cria campo emitir ato autorizativo em configurações gerais.
- Adiciona flag na validação de turmas com horários conflitantes para verificar somente turmas informadas ao Educacenso.
- Adiciona novas colunas na view histórico 9 anos
- Suporte ao Apache HTTP Server [#592](https://github.com/portabilis/i-educar/pull/592).

### Changed

- Ajusta nome do arquivo exportado para o Educacenso.
- Altera campo certidão de nascimento novo formato para só aceitar números.
- Altera campo certidão de casamento novo formato para só aceitar números.
- Altera tamanho máximo de alguns inputs para evitar erro ao salvar.
- Utiliza a versão 74 do Chrome driver  [#595](https://github.com/portabilis/i-educar/pull/595).

### Fixed

- Valida duplicidade em nomes de turma somente em turmas ativas e visíveis.
- Ajusta funcionamento da modal de seleção de PJ no cadastro de escola.
- Inverte a ordem dos comandos de instalação [#594](https://github.com/portabilis/i-educar/pull/594).

## [2.1.15] - 2019-06-10

Notas completas da versão: [2.1.15](https://github.com/portabilis/i-educar/releases/tag/2.1.15).

### Added

- Suporte ao Apache HTTP Server #592.
- Adiciona coluna `report.termo_recuperacao_final` em custom labels.

### Changed

- Adia data de exportação.
- Ajusta final do arquivo de exportação.
- Desfaz regra de CPF nulos para pessoas estrangeiras.
- Trata erro em campos array na importação do arquivo do censo.
- Exige município de nascimento apenas para alunos brasileiros.
- Trata erros em casos de turmas sem componentes curriculares na análise do registro 20.
- Utiliza a versão 74 do Chrome driver para rodar testes no CI #595.

### Fixed

- Corrige exportação do código INEP de pais de origem.
- Corrige a ordem dos comandos do instalador #594.

## [2.1.14] - 2019-06-03

Notas completas da versão: [2.1.14](https://github.com/portabilis/i-educar/releases/tag/2.1.14).

### Added

- Adiciona colunas referente a calça jeans em distribuição de uniforme.
- Implementa exceção na validação de matrículas no mesmo horário.

### Changed

- Mensagens e notificações melhoradas.
- Melhora o importar de componentes curriculares.
- Mantém os filtros de busca nas páginas de listagem.
- Atualiza imagem do PHP no Docker para realizar build.

### Fixed

- Corrige busca de componentes curriculares do registro 20.
- Parâmetro para determinar data limite de horários conflitantes.
- Corrige verificação de função professor.

## [2.1.13] - 2019-05-27

Notas completas da versão: [2.1.13](https://github.com/portabilis/i-educar/releases/tag/2.1.13).

### Added

- Adicionar o campo observação a tabela de arredondamento.

### Changed

- Melhora a validação de enturmação no mesmo horário.
- Não exporta pessoas vinculadas à turmas não exportadas.
- Ajusta campo deficiências na análise do registro 30.
- Ajusta mensagem de exportação bloqueada.

### Fixed

- Valida deficiências na interface.
- Valida localização diferenciada da escola na análise do censo.
- Corrige o breadcrumb do quadro de horários.
- Corrige unificação de vínculos da pessoa.
- Corrige data final do ano letivo da turma.
- Retorna conteúdo do PDF na factory PHP Jasper #577.
- Correções nos scripts de instalação #578.

## [2.1.12] - 2019-05-20

Notas completas da versão: [2.1.12](https://github.com/portabilis/i-educar/releases/tag/2.1.12).

### Changed

- Ajusta breadcrumbs de páginas antigas.
- Endpoint para buscar apenas matrículas transferidas.
- Não permite o registro duplicado de notas, faltas e pareceres.
- Registro 30.
- Ajusta opções do campo Recebe Escolarização.
- Ajusta mensagem de validação de deficiências no registro 30.

### Removed

- Remove botão Etapa da turma unificada do detalhe da Matrícula.

### Fixed

- Valida se componente existe na série da escola.
- Corrige problema que o menu fica por baixo do select.
- Migra menus esquecidos.
- Exibe campo INEP no cadastro de escolas.

## [2.1.11] - 2019-05-14

Notas completas da versão: [2.1.11](https://github.com/portabilis/i-educar/releases/tag/2.1.11).

### Added

- Permite filtrar nome de municípios com/sem acentos.

### Changed

- Centraliza pop-ups do sistema.
- Migra sistema de menus.
- Registro 20.
- Registro 40.
- Registro 50.
- Registro 60.
- Altera data do Educacenso na instituição para a data base de 2019.

### Fixed

- Corrige problema de arredondamento da média.
- Remove registros duplicados de lançamento de notas, faltas e pareceres.
- Corrige o cadastro do log de acesso.
- Corrige erro na query que busca matrículas.
- Corrige checkbox em séries da escola #289.
- Corrige pop-up que busca cliente da biblioteca.
- Corrige `slug` da aplicação no Tag Manager.
- Migra menus esquecidos.
- Corrige CSS do menu ficava por baixo do select.

## [2.1.10] - 2019-05-07

Notas completas da versão: [2.1.10](https://github.com/portabilis/i-educar/releases/tag/2.1.10).

### Added

- Permite "modifiers" aos relatórios.

### Changed

- Utiliza timezone do arquivo `.ini` nos relatórios.
- Ajusta mensagem de exportação do censo.

### Removed

- Remove registros duplicados de lançamentos de notas, faltas e pareceres.

### Fixed

- Corrige erro na query ao buscar matrículas.
- Corrige o log de acesso.
- Exibe CPF na listagem de servidores.
- Corrige campos que não apagam no cadastro de turmas.

## [2.1.9] - 2019-04-29

Notas completas da versão: [2.1.9](https://github.com/portabilis/i-educar/releases/tag/2.1.9).

### Added

- Habilita anos letivos retroativos.
- Cria configuração para desabilitar exportação do censo.

### Changed

- Middleware para não permitir login de contas suspensas.
- Faz track apenas de exceções necessárias.
- Substitui as antigas páginas de erros.
- Alterações do registro 10.
- Ajustes na mensagem de exportação do censo.

### Removed

- Remove páginas de cadastro de menus (arquitetura antiga).
- Remove classe `clsPmiajudaPagina`.
- Remove regra de análise do campo "código do cartório".

### Fixed

- Valida exclusão da Fórmula da Média.
- Corrige erro de digitação.
- Ajusta migrations para casos que não encontra foreign key.
- Corrige definição de aluno como falecido sem notas na matrícula.
- Converte arquivo PDF para base64 para retornar via JSON.
- Ajusta resposta da API para ficar no padrão aos demais endpoints.
- Corrige busca de tenant quando endereço possui hífen.
- Corrige erros no console do browser ao utilizar a busca rápida.
- Adiciona parâmetro situação na API do boletim do professor.

## [2.1.8] - 2019-04-15

Notas completas da versão: [2.1.8](https://github.com/portabilis/i-educar/releases/tag/2.1.8).

### Added

- Implementa o compartilhamento de erros do Laravel.

### Changed

- Utiliza funcionalidades nativas do Laravel.
  - [Sessão](https://laravel.com/docs/master/session).
  - [Autenticação](https://laravel.com/docs/master/authentication).
  - [Mail](https://laravel.com/docs/master/mail).
- Padroniza páginas públicas com o mesmo layout da página de login.
- Melhora telas e processo de enturmação em lote.
- Utiliza `HttpResponseException` ao invés de encerrar a aplicação com `die`.
- Move informações do Google Tag Manager para `legacy.php`.

### Fixed

- Ajusta turno das enturmações.
- Ajusta update para que pegue apenas matriculas com vinculo na turma.
- Não duplica track de erro.
- Corrige exibição da matrícula do servidor.

## [2.1.7] - 2019-04-08

Notas completas da versão: [2.1.7](https://github.com/portabilis/i-educar/releases/tag/2.1.7).

### Added

- Cria regras de exportação.

### Changed

- Comandos para importador componentes curriculares.
- Registro 00.
- Desabilita temporariamente a exportação do censo.
- Constantes.
- Models, factories e testes.
- Trait para auxiliar na criação de views.
- Melhora a utilização de sessão no i-Educar.
- Limpeza de arquivos.
- Aplica PSR-2 em diversos arquivos.

### Fixed

- Corrige erro ao criar e excluir compromisso issue #549, pull requests #550 e #551.
- Corrige acentuação na tela de cadastro de exemplares.
- Corrige query que pega o total de registros na listagem em Reserva de Vaga.

## [2.1.6] - 2019-03-19

Notas completas da versão: [2.1.6](https://github.com/portabilis/i-educar/releases/tag/2.1.6).

### Added

- Implementa redirecionamento via exceção para o código legado.

### Changed

- Melhora integração de enturmações entre i-Diário e i-Educar.
  - Cria coluna `id` na tabela `pmieducar.matricula_turma`.
  - Cria tabela `pmieducar.matricula_turma_excluidos` e trigger para salvar enturmações excluídas.
  - Retorna o ID da enturmação via API.
  - Endpoint para retornar as enturmações excluídas.
  - Atualiza trigger de auditoria de enturmações.
- Retorna o nome das etapas via API.
- Aplica PSR-2 em diversos arquivos.

### Fixed

- Corrige apresentação de município em busca.

## [2.1.5] - 2019-03-11

Notas completas da versão: [2.1.5](https://github.com/portabilis/i-educar/releases/tag/2.1.5).

### Changed

- Move o turno da matrícula para a enturmação.
  - Remove a coluna `turno_id` da tabela `matricula`.
  - Adiciona a coluna `turno_id` na tabela `matricula_turma`.
- Bloqueia a alteração da média após modificação manual.
- Trait de suporte para criação de views.
- Usa o campo `cod_servidor_funcao` para determinar edição ou inserção.
- Atualiza interface com o banco para realização de consultas.
- Mensagem de alerta ao alterar a função do servidor.
- Não retorna as matrículas de alunos inativos via API.
- Aplica PSR-2 em diversos arquivos.

### Removed

- Remove o arquivo `custom_labels.json` movendo para o banco de dados.
- Remove a interceptação de exceções na `clsBase`.

### Fixed

- Corrige bug ao listar enturmações.
- Corrige persistência de preferências.
- Corrige bug ao tentar remover a seleção do modelo de boletim diferenciado.
- Corrige migration que adiciona tipos de logradouros.

## [2.1.4] - 2019-02-28

Notas completas da versão: [2.1.4](https://github.com/portabilis/i-educar/releases/tag/2.1.4).

### Added

- Cria novas exceções para determinados erros.
- Cria configuração para pasta de cache.
- Adiciona mensagem para erro ao matricular aluno.

### Changed

- Melhorias na montagem do layout do i-Educar através de templates Blade.
- Middleware para troca de tenant.
- Refatorações e correções no módulo de faltas e notas.
- Models do Eloquent para dar inicio a refatoração do banco de dados.
- Desabilita o Xdebug por default utilizando Docker.
- Atualizar versão do Composer no container `php`.
- Refatora alguns testes unitários.
- Aplica PSR-2 em diversos arquivos.

### Removed

- DBUnit.
- mikey179/vfsStream.

### Fixed

- Corrige a API de promoção de matrículas para considerar dispensas.
- Ajusta status da enturmação ao marcar matrícula como falecido.
- Adicionado itens no atestado de vaga.
- Corrige bug nos campos de etapas ao mudar de aba.
- Ajustado histórico escolar em lote.
- Corrige bug nas modais.
- Corrige funcionalidade de abandono.

## [2.1.3] - 2019-02-08

Notas completas da versão: [2.1.3](https://github.com/portabilis/i-educar/releases/tag/2.1.3).

### Added

- Adiciona mais opções de turnos no módulo de transporte.
- Insere tipos de logradouros: "POVO" e "LOCAL".
- Adiciona campo ano letivo para filtrar regra de avaliação em séries.
- Cria códigos para erros da API.
- Cria middleware para permitir multi-tenant usando a mesma aplicação.

### Changed

- Escapa caracteres no cadastro de endereços.
- Marca enturmação como remanejada se for após data base.
- Ajusta mensagens de validação.
- Aplica PSR-2 em diversos arquivos.

### Removed

- Remove duplos espaços em branco no nome da pessoa.

### Fixed

- Corrige situação da matrícula quando reprovado por faltas e dependência.
- Valida valores da tabela de arredondamento.
- Corrige problema ao tentar atualizar média de aluno sem notas lançadas.
- Corrige o campo `SUS` do cadastro de pessoa.
- Corrige problema ao tentar fazer download de relatório.
- Corrige cadastro do campo "Religião".
- Corrige problema ao cancelar uma solicitação de transferência sem enturmação.
- Corrige forma de atualizar a situação da matrícula.
- Ajustes para permitir remover a informação multiseriada.

## [2.1.2] - 2019-01-22

Notas completas da versão: [2.1.2](https://github.com/portabilis/i-educar/releases/tag/2.1.2).

### Added

- Adiciona opção para não compilar relatórios no comando `reports:install`.
- Adiciona nova label para personalizar relatórios.
- Adiciona os tokens `En` quando há dispensa de etapas.
- Adiciona alerta para mostrar erro na validação da enturmação.
- Cria campo `bloquear_cadastro_aluno` nas configurações gerais.

### Changed

- Altera `robots.txt` para bloquear searchbots.
- Garante que o 0 não será usado como `tipo_nota`.
- Garante que o valor de `carga_horaria` será sempre um inteiro.
- Muda como é salvo os valores da auditoria.
- Move a pasta de migrações dos relatórios.
- Aplica PSR-2 em diversos arquivos.

### Removed

- Exclui realmente o registro da tabela `pmieducar.sequencia_serie`.

### Fixed

- Corrige query na busca de matrícula.
- Corrige situação da matrícula quando retido por faltas.

## [2.1.1] - 2018-12-21

Notas completas da versão: [2.1.1](https://github.com/portabilis/i-educar/releases/tag/2.1.1).

### Added

- Adiciona base curricular as disciplinas do histórico escolar.
- Permite requisições à API de promoção de matrículas utilizando `access_key` e `secret_key`.
- Permite lançar nota do exame final, mesmo para alunos retidos por faltas.

### Changed

- [Comando para restaurar o banco de dados a partir de um backup](https://github.com/portabilis/i-educar/wiki/comando-para-restaurar-o-banco-de-dados-a-partir-de-um-backup).
- Comando para compilar os arquivos `.jrxml` do pacote de relatórios.
- Aumenta o tamanho da coluna `cartorio_cert_civil` da tabela `historico.documento`.
- Retorna via API, apenas as disciplinas vinculadas ao professor no ano filtrado.
- Ao remover uma nota, não recalcula a média utilizando a nota como 0.
- Aplica PSR-2 em diversos arquivos.
- Utiliza o valor correto da carga horária no processamento do histórico escolar.

### Removed

- Remove função que deleta notas, faltas e médias quando o vínculo entre turma e disciplina é removido.

### Fixed

- Valida se a nota lançada via API, está dentro do limite estabelecido na regra de avaliação.
- Valida campos antes de salvar a tela de etapas.
- Corrige bug da tela de calendário que não permitia que o usuário navegasse entre os anos e meses do calendário.
- Corrige cálculos da formula da média.
- Corrige problemas com a soma das etapas `Se` quando utilizadas etapas específicas.
- Corrige funcionamento ao adicionar nova enturmação.

## [2.1.0] - 2018-11-30

Notas completas da versão: [2.1.0](https://github.com/portabilis/i-educar/releases/tag/2.1.0).

### Added

- Adicionado o [Laravel Telescope](https://laravel.com/docs/telescope) em desenvolvimento.

### Changed

- [Instalação fácil para desenvolvedores](https://github.com/portabilis/i-educar/#instala%C3%A7%C3%A3o).
- [Interface visual de instalação em servidores web](https://github.com/portabilis/i-educar#instala%C3%A7%C3%A3o-em-servidor).
- [Upgrade da versão 2.0.x para 2.1.x](https://github.com/portabilis/i-educar/#upgrade-para-21).
- As imagens Docker utilizadas pelo i-Educar foram refeitas.
- Configurações mais simplificadas.
- Testes automatizados foram reestruturados.
- O projeto foi reorganizado.
- Melhorado o autoload de classes.
- A estrutura inicial do banco de dados foi atualizada e melhorada.
- O i-Educar está sendo executado através do [Laravel](https://laravel.com/).
- A biblioteca Phinx foi removida em prol das [migrations](https://laravel.com/docs/migrations) do Laravel.
- Sistema de track de erros e exceções.
- Debug com [Whoops](https://github.com/filp/whoops).
- Nome social no cadastro de pessoa física.
- Loga as queries executadas do código legado.
- Traz as matriculas de alunos transferidos pela API.
- Ajusta query para não mostrar turmas inativas.

### Removed

- Remove verificação do campo `turmas_por_ano` ao replicar turmas do ano letivo anterior.

### Fixed

- Valida datas de enturmação na enturmação em lote.
- Valida data de matrícula na rematrícula automática.
- Corrige a busca do aluno.
- Corrige ordenação da listagem de alunos.
- Corrige bug na transferência da matrícula.
- Corrige API que estava duplicando turma em regras diferentes.

## [2.0.11] - 2018-11-30

Notas completas da versão: [2.0.11](https://github.com/portabilis/i-educar/releases/tag/2.0.11).

### Added

- Adiciona de coluna para nome social.

### Changed

- Recria a view de consulta do cadastro de pessoas físicas.

## [2.0.10] - 2018-11-12

Notas completas da versão: [2.0.10](https://github.com/portabilis/i-educar/releases/tag/2.0.10).

### Added

- Cria helper para multiplos checkboxes.
- Adiciona observações à tabela de endereços.

### Changed

- Ajusta API que retorna as regras de avaliações e as turmas.
- Melhora definição de null para campo de trabalho.
- Retorna CPF nos dados do aluno via API.
- Retorna turno do aluno e professor na API.
- Aplica PSR-2 em diversos arquivos

### Fixed

- Corrige bug na tela de cadastro do aluno.
- Corrige método da listagem de componentes da turma.
- Corrige diversos pequenos bugs.

## [2.0.9] - 2018-10-29

Notas completas da versão: [2.0.9](https://github.com/portabilis/i-educar/releases/tag/2.0.9).

### Added

- Adiciona campo  **termo assinatura do secretario** na customização de label.

### Changed

- Ajusta campos do cadastro de pessoa que não estavam sendo exibidos no cadastro de aluno.
- Desconsidera situação transferido na validação de sequencia de enturmação.
- Não exibe sexo do aluno se o campo estiver vazio na tela de detalhe do aluno.
- Utiliza corretamente `substitui_menor_nota` nas regras de recuperação paralela.
- Ajusta link e mensagem de análise do registro 50.

### Fixed

- Corrige bug ao criar novo ano letivo na escola.
- Corrige a verificação de múltiplas matrícula do aluno.

## [2.0.8] - 2018-10-10

Notas completas da versão: [2.0.8](https://github.com/portabilis/i-educar/releases/tag/2.0.8).

### Added

- Telas de consulta do movimento geral e mensal.
- Cria arquivo de configuração para build no semaphore CI.
- Desabilita o envio de e-mail de erros e transforma em um tracker,

### Changed

- Melhorias na antiga tela de módulos que agora se chama etapa.
- Retorna foto do aluno na api de alunos.
- Altera a dinâmica para a situação do componente do aluno ao sofrer alterações na matricula.
- Retorna somente turmas e etapas ativas na api.
- Ajusta auditoria geral para mostrar json completo das alterações.
- Adiciona o item Camiseta infantil sem manga a distribuição de uniformes.
- Prevê cálculo de média ponderada após dispensa de etapas.
- Ajusta api de matricula para trazer as enturmações sem validar a regra da data base.
- Possibilita seleção de múltiplas disciplinas (por área de conhecimento) na dispensa por etapa.
- Aplica PSR-2 em diversos arquivos

### Fixed

- Corrige bugs na api de deficiência.
- Corrige bug ao retornar deficiência na api de aluno.
- Corrige bug da trigger de auditoria falta geral.
- Resolve problema ao registrar dependência para o aluno com regra diferenciada.
- Corrige problema na busca com espaço no MultipleSearch.
- Corrige validação de datas no cadastro de turmas.
- Corrige diversos pequenos bugs.

## [2.0.7] - 2018-09-17

Notas completas da versão: [2.0.7](https://github.com/portabilis/i-educar/releases/tag/2.0.7).

### Added

- Adiciona regra para caso a instituição não use data base de remanejamento não retorna as enturmações remanejadas;
- Permite calcular a média ponderada levando em consideração etapas dispensadas.
- Adiciona triggers no banco para impedir duplicidade de lançamentos de notas, pareceres e faltas;

### Changed

- Altera o sistema para trabalhar de acordo com anos letivos;
- Integra com HoneyBadger para tracking de erros (opcional);
- Aplica PSR-2 em diversos arquivos.

### Removed

- Remove botão "Imprimir Arquivo" que aparecia em todas as telas de listagem;

### Fixed

- Corrige problema com o input nome da escola no histórico do aluno quando a opção "Outra Escola" é selecionada;
- Corrige diversos trechos de códigos incorretos.

## [2.0.6] - 2018-08-27

Notas completas da versão: [2.0.6](https://github.com/portabilis/i-educar/releases/tag/2.0.6).

### Added

- Cria triggers para impedir duplicação nas tabelas `nota_aluno`, `parecer_aluno` e `falta_aluno`
- Cria teste de regressão na exibição de campos do tipo arquivo
- Cria teste de regressão na API de regras de avaliação
- Implementa triggers para gravar auditoria no lançamento de notas, faltas, matrículas e enturmações
- Implementa auditoria no vínculo entre professor_turma e professor_disciplina
- Cria tela para exibir detalhes dos dados alterados e usuário responsável

### Changed

- Implentação de PSR-2 em alguns arquivos
- Melhora os filtros da tela de auditoria

### Fixed

- Corrige bug na exibição de campos do tipo arquivo
- Corrige bug na API de regras de avaliação que estava retornando dados duplocados

## [2.0.5] - 2018-08-10

Notas completas da versão: [2.0.5](https://github.com/portabilis/i-educar/releases/tag/2.0.5).

### Added

- Implementa regra para o preenchimento do campo 24 do registro 60;
- Adicionado verificação extra para remoção de turmas;
- Adicionado travas de remoção nas séries;
- Adicionado campo para desconsiderar deficiência na regra diferenciada;
- Adicionada validação para arredondamento de nota e média;
- Implementados alguns testes.

### Changed

- Refatora regra do campo 19 registro 20;
- Ajustada a turma unificada na enturmação para a exportação do Censo;
- Refatora validação de deficiência múltipla e cria configuração de deficiência múltipla do professor;
- Ajusta para não inativar solicitação transferência quando atendida;
- Altera a busca de alunos com deficiências para desconsiderar alguns casos.
- Migrado campo `turma_unificada` da turma para enturmação da matrícula;
- Ajustada condição para formatar nota;
- Ajustada validação turma unificada;
- Migrado para o service boletim a validação de notas e faltas das etapas anteriores;
- Aplicada PSR-2 em alguns arquivos;

### Removed

- Remove caracteres especiais do RG na exportação do Censo, registro 70;

### Fixed

- Corrige variável que representa o campo 26 do registro 30;
- Corrige bug nos campos 24 e 39;
- Corrige problema ao salvar o código INEP da escola;
- Corrige bloqueio dos campos horas no cadastro turma;
- Valida se as notas das etapas anteriores foram lançadas na API;
- Corrigido diversos notices e warnings;

## [2.0.4] - 2018-07-17

Notas completas da versão: [2.0.4](https://github.com/portabilis/i-educar/releases/tag/2.0.4).

### Added

- Adiciona na interface do cadastro de alocação de servidores os campos de horário (início, término, atividade, excedentes);
- Adicionado helper de seleção de componentes no vínculo de professores;

### Changed

- Considera apenas enturmações ativas na análise do registro 80;
- Aplicada regra do Educacenso no campo 9 do registro 80;
- Ajustada query de análise do registro 80 para ficar igual a query da exportação dos resultados;
- Alterado o campo `nm_disciplina` da tabela `pmieducar.historico_disciplinas` para tipo `text`;
- Aplicada PSR-2 em alguns arquivos;

### Fixed

- Corrigido retorno da situação somente na última etapa e/ou 'RC' na API de boletim;
- Corrigido problema com o campo raça no cadastro de pessoas que não estava permitindo salvar o seu valor corretamente;
- Corrigido diversos notices e warnings;

## [2.0.3] - 2018-06-29

Notas completas da versão: [2.0.3](https://github.com/portabilis/i-educar/releases/tag/2.0.3).

### Added

- Adiciona validação para não permitir remoção de tipo de usuário com usuários relacionados;

### Changed

- Ajusta preenchimento campos 30 a 38 para 0 caso 39 for 1;
- Altera regra de curso superior de servidor na exportação do censo;
- Altera query do registro 00 pra pegar endereço de escolas com dados cadastrados na tabela endereco_externo;
- Converte todos os campos do registro 70 para caixa alta e sem acentos;
- Altera função modules.frequencia_por_componente;
- Define valor padrão para parâmetro `str_passaporte` na classe `clsDocumento`;
- Configura code sniffer.

### Fixed

- Corrige problema no campo 87 (Banda larga) do registro 10 do Educacenso.
- Corrige select box raça no cadastro de pessoas;
- Corrige falha na cópia de enturmações em lote;

[Unreleased]: https://github.com/portabilis/i-educar/compare/2.11.0...HEAD
[2.11.0]: https://github.com/portabilis/i-educar/compare/2.10.0...2.11.0
[2.10.0]: https://github.com/portabilis/i-educar/compare/2.9.0...2.10.0
[2.9.0]: https://github.com/portabilis/i-educar/compare/2.8.0...2.9.0
[2.8.0]: https://github.com/portabilis/i-educar/compare/2.8.0-beta...2.8.0
[2.8.0-beta]: https://github.com/portabilis/i-educar/compare/2.7.5...2.8.0-beta
[2.7.5]: https://github.com/portabilis/i-educar/compare/2.7.4...2.7.5
[2.7.4]: https://github.com/portabilis/i-educar/compare/2.7.3...2.7.4
[2.7.3]: https://github.com/portabilis/i-educar/compare/2.7.2...2.7.3
[2.7.2]: https://github.com/portabilis/i-educar/compare/2.7.1...2.7.2
[2.7.1]: https://github.com/portabilis/i-educar/compare/2.7.0...2.7.1
[2.7.0]: https://github.com/portabilis/i-educar/compare/2.6.9...2.7.0
[2.6.9]: https://github.com/portabilis/i-educar/compare/2.6.8...2.6.9
[2.6.8]: https://github.com/portabilis/i-educar/compare/2.6.7...2.6.8
[2.6.7]: https://github.com/portabilis/i-educar/compare/2.6.6...2.6.7
[2.6.6]: https://github.com/portabilis/i-educar/compare/2.6.5...2.6.6
[2.6.5]: https://github.com/portabilis/i-educar/compare/2.6.4...2.6.5
[2.6.4]: https://github.com/portabilis/i-educar/compare/2.6.3...2.6.4
[2.6.3]: https://github.com/portabilis/i-educar/compare/2.6.2...2.6.3
[2.6.2]: https://github.com/portabilis/i-educar/compare/2.6.1...2.6.2
[2.6.1]: https://github.com/portabilis/i-educar/compare/2.6.0...2.6.1
[2.6.0]: https://github.com/portabilis/i-educar/compare/2.5.4-upgrade...2.6.0
[2.5.4-upgrade]: https://github.com/portabilis/i-educar/compare/2.5.3...2.5.4-upgrade
[2.5.3]: https://github.com/portabilis/i-educar/compare/2.5.2...2.5.3
[2.5.2]: https://github.com/portabilis/i-educar/compare/2.5.1...2.5.2
[2.5.1]: https://github.com/portabilis/i-educar/compare/2.5.0...2.5.1
[2.5.0]: https://github.com/portabilis/i-educar/compare/2.4.3...2.5.0
[2.4.3]: https://github.com/portabilis/i-educar/compare/2.4.2...2.4.3
[2.4.2]: https://github.com/portabilis/i-educar/compare/2.4.1...2.4.2
[2.4.1]: https://github.com/portabilis/i-educar/compare/2.4.0...2.4.1
[2.4.0]: https://github.com/portabilis/i-educar/compare/2.3.10...2.4.0
[2.3.10]: https://github.com/portabilis/i-educar/compare/2.3.9...2.3.10
[2.3.9]: https://github.com/portabilis/i-educar/compare/2.3.8...2.3.9
[2.3.8]: https://github.com/portabilis/i-educar/compare/2.3.7...2.3.8
[2.3.7]: https://github.com/portabilis/i-educar/compare/2.3.6...2.3.7
[2.3.6]: https://github.com/portabilis/i-educar/compare/2.3.5...2.3.6
[2.3.5]: https://github.com/portabilis/i-educar/compare/2.3.4...2.3.5
[2.3.4]: https://github.com/portabilis/i-educar/compare/2.3.3...2.3.4
[2.3.3]: https://github.com/portabilis/i-educar/compare/2.3.2...2.3.3
[2.3.2]: https://github.com/portabilis/i-educar/compare/2.3.1...2.3.2
[2.3.1]: https://github.com/portabilis/i-educar/compare/2.3.0...2.3.1
[2.3.0]: https://github.com/portabilis/i-educar/compare/2.2.22-upgrade...2.3.0
[2.2.22-upgrade]: https://github.com/portabilis/i-educar/compare/2.2.21-upgrade...2.2.22-upgrade
[2.2.21-upgrade]: https://github.com/portabilis/i-educar/compare/2.2.20...2.2.21-upgrade
[2.2.20]: https://github.com/portabilis/i-educar/compare/2.2.19...2.2.20
[2.2.19]: https://github.com/portabilis/i-educar/compare/2.2.18...2.2.19
[2.2.18]: https://github.com/portabilis/i-educar/compare/2.2.17...2.2.18
[2.2.17]: https://github.com/portabilis/i-educar/compare/2.2.16...2.2.17
[2.2.16]: https://github.com/portabilis/i-educar/compare/2.2.15...2.2.16
[2.2.15]: https://github.com/portabilis/i-educar/compare/2.2.14...2.2.15
[2.2.14]: https://github.com/portabilis/i-educar/compare/2.2.13...2.2.14
[2.2.13]: https://github.com/portabilis/i-educar/compare/2.2.12...2.2.13
[2.2.12]: https://github.com/portabilis/i-educar/compare/2.2.11...2.2.12
[2.2.11]: https://github.com/portabilis/i-educar/compare/2.2.10...2.2.11
[2.2.10]: https://github.com/portabilis/i-educar/compare/2.2.9...2.2.10
[2.2.9]: https://github.com/portabilis/i-educar/compare/2.2.8...2.2.9
[2.2.8]: https://github.com/portabilis/i-educar/compare/2.2.7...2.2.8
[2.2.7]: https://github.com/portabilis/i-educar/compare/2.2.6...2.2.7
[2.2.6]: https://github.com/portabilis/i-educar/compare/2.2.5...2.2.6
[2.2.5]: https://github.com/portabilis/i-educar/compare/2.2.4...2.2.5
[2.2.4]: https://github.com/portabilis/i-educar/compare/2.2.3...2.2.4
[2.2.3]: https://github.com/portabilis/i-educar/compare/2.2.2...2.2.3
[2.2.2]: https://github.com/portabilis/i-educar/compare/2.2.1...2.2.2
[2.2.1]: https://github.com/portabilis/i-educar/compare/2.2.0...2.2.1
[2.2.0]: https://github.com/portabilis/i-educar/compare/2.1.25-upgrade...2.2.0
[2.1.25-upgrade]: https://github.com/portabilis/i-educar/compare/2.1.24-upgrade...2.1.25-upgrade
[2.1.24-upgrade]: https://github.com/portabilis/i-educar/compare/2.1.23-upgrade...2.1.24-upgrade
[2.1.23-upgrade]: https://github.com/portabilis/i-educar/compare/2.1.22-upgrade...2.1.23-upgrade
[2.1.22-upgrade]: https://github.com/portabilis/i-educar/compare/2.1.21...2.1.22-upgrade
[2.1.21]: https://github.com/portabilis/i-educar/compare/2.1.20...2.1.21
[2.1.20]: https://github.com/portabilis/i-educar/compare/2.1.19...2.1.20
[2.1.19]: https://github.com/portabilis/i-educar/compare/2.1.18...2.1.19
[2.1.18]: https://github.com/portabilis/i-educar/compare/2.1.17...2.1.18
[2.1.17]: https://github.com/portabilis/i-educar/compare/2.1.16...2.1.17
[2.1.16]: https://github.com/portabilis/i-educar/compare/2.1.15...2.1.16
[2.1.15]: https://github.com/portabilis/i-educar/compare/2.1.14...2.1.15
[2.1.14]: https://github.com/portabilis/i-educar/compare/2.1.13...2.1.14
[2.1.13]: https://github.com/portabilis/i-educar/compare/2.1.12...2.1.13
[2.1.12]: https://github.com/portabilis/i-educar/compare/2.1.11...2.1.12
[2.1.11]: https://github.com/portabilis/i-educar/compare/2.1.10...2.1.11
[2.1.10]: https://github.com/portabilis/i-educar/compare/2.1.9...2.1.10
[2.1.9]: https://github.com/portabilis/i-educar/compare/2.1.8...2.1.9
[2.1.8]: https://github.com/portabilis/i-educar/compare/2.1.7...2.1.8
[2.1.7]: https://github.com/portabilis/i-educar/compare/2.1.6...2.1.7
[2.1.6]: https://github.com/portabilis/i-educar/compare/2.1.5...2.1.6
[2.1.5]: https://github.com/portabilis/i-educar/compare/2.1.4...2.1.5
[2.1.4]: https://github.com/portabilis/i-educar/compare/2.1.3...2.1.4
[2.1.3]: https://github.com/portabilis/i-educar/compare/2.1.2...2.1.3
[2.1.2]: https://github.com/portabilis/i-educar/compare/2.1.1...2.1.2
[2.1.1]: https://github.com/portabilis/i-educar/compare/2.1.0...2.1.1
[2.1.0]: https://github.com/portabilis/i-educar/compare/2.0.11...2.1.0
[2.0.11]: https://github.com/portabilis/i-educar/compare/2.0.10...2.0.11
[2.0.10]: https://github.com/portabilis/i-educar/compare/2.0.9...2.0.10
[2.0.9]: https://github.com/portabilis/i-educar/compare/2.0.8...2.0.9
[2.0.8]: https://github.com/portabilis/i-educar/compare/2.0.7...2.0.8
[2.0.7]: https://github.com/portabilis/i-educar/compare/2.0.6...2.0.7
[2.0.6]: https://github.com/portabilis/i-educar/compare/2.0.5...2.0.6
[2.0.5]: https://github.com/portabilis/i-educar/compare/2.0.4...2.0.5
[2.0.4]: https://github.com/portabilis/i-educar/compare/2.0.3...2.0.4
[2.0.3]: https://github.com/portabilis/i-educar/releases/tag/2.0.3
