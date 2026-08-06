# i-Educar — Requisitos e Regras de Negócio (Módulo Web)

> Documento de referência para QA — análise de requisitos e regras de negócio,
> usado como base para elaboração de casos de teste, identificação de cenários
> críticos e priorização de testes exploratórios.
>
> Fonte: documento funcional do i-Educar (versão 2.12), com foco na camada web.

## Sumário

1. [Acesso e Segurança](#1-acesso-e-segurança)
2. [Cadastros Gerais (Pessoas, Escolas, Turmas)](#2-cadastros-gerais-pessoas-escolas-turmas)
3. [Matrícula](#3-matrícula)
4. [Enturmação (Vínculo Aluno-Turma)](#4-enturmação-vínculo-aluno-turma)
5. [Transferência e Unificação de Cadastros](#5-transferência-e-unificação-de-cadastros)
6. [Avaliação e Boletim](#6-avaliação-e-boletim)
7. [Frequência e Situação Final](#7-frequência-e-situação-final)
8. [Censo Escolar (Educacenso) e Exportações Oficiais](#8-censo-escolar-educacenso-e-exportações-oficiais)
9. [Dispensa de Disciplina](#9-dispensa-de-disciplina)
10. [Comunicação (Avisos e Notificações)](#10-comunicação-avisos-e-notificações)
11. [Configurações e Administração](#11-configurações-e-administração)
12. [Upload e Gestão de Arquivos](#12-upload-e-gestão-de-arquivos)
13. [Requisitos não funcionais gerais](#13-requisitos-não-funcionais-gerais)

---

## 1. Acesso e Segurança

### Requisitos

- O sistema deve permitir login de usuários cadastrados (não há autocadastro público).
- O sistema deve suportar login via provedor externo (SSO/social login).
- O sistema deve permitir troca de senha pelo próprio usuário.
- O sistema deve exibir menus e funcionalidades de acordo com o perfil do usuário logado.

### Regras de negócio

- Cada usuário possui um tipo/perfil de acesso, e cada perfil tem permissões
  específicas por funcionalidade (visualizar e/ou modificar), configuradas de
  forma independente.
- Um usuário pode ser obrigado a trocar a senha antes de continuar navegando
  (ex.: senha expirada ou definida como temporária).
- Uma instituição pode ter o acesso suspenso (ex.: inadimplência, bloqueio
  administrativo); nesse caso, os usuários vinculados a ela não conseguem
  operar o sistema.
- O sistema é multi-instituição: os dados de uma instituição não podem ser
  vistos/acessados por usuários de outra.

**Pontos de atenção para QA:** isolamento entre instituições (teste de acesso
cruzado), bloqueio efetivo de instituição suspensa, obrigatoriedade real da
troca de senha temporária/expirada, e permissões granulares (visualizar ≠
modificar) por perfil.

---

## 2. Cadastros Gerais (Pessoas, Escolas, Turmas)

### Requisitos

- Cadastro de pessoas (alunos, servidores, responsáveis) com dados pessoais,
  endereço, documentação, raça/cor, religião e deficiência.
- Cadastro de instituições, escolas, cursos, séries/escolaridade,
  disciplinas/componentes curriculares e turmas.
- Cadastro de calendário letivo (dias letivos, feriados, eventos).

### Regras de negócio

- Exclusões de cadastros geralmente são lógicas (o registro é inativado, não
  removido do banco), preservando histórico.
- Determinados campos cadastrais (endereço completo, documentação, raça/cor,
  deficiência) são obrigatórios para permitir a exportação de dados ao Censo
  Escolar.
- Uma pessoa marcada como falecida deve ter seu cadastro tratado de forma
  diferenciada (ex.: bloqueio de novas matrículas).

**Pontos de atenção para QA:** exclusão lógica vs. física (o registro some da
listagem mas continua íntegro no banco?), validação de obrigatoriedade de
campos ligados ao Censo, e bloqueio de matrícula para pessoa falecida.

---

## 3. Matrícula

### Requisitos

- Efetuar matrícula de aluno em uma escola/série/ano letivo.
- Consultar e listar matrículas por turma.
- Atualizar a situação da matrícula (ativa, transferida, abandono, cancelada,
  etc.).
- Atualizar a data de entrada da matrícula.
- Reclassificar matrícula (mudança de série/etapa sem nova matrícula).

### Regras de negócio

- A data de matrícula deve ser compatível com o ano letivo da turma/escola
  (não pode ser anterior nem posterior ao período letivo vigente).
- Não é permitido registrar uma nova matrícula em conflito de datas com uma
  matrícula já ativa do mesmo aluno.
- Alterações de situação da matrícula ficam sujeitas a permissão específica e
  geram rastro para consulta posterior.

**Pontos de atenção para QA:** validação de datas fora do ano letivo (limites
inferior e superior), tentativa de matrícula duplicada/conflitante, e
auditoria das mudanças de situação.

---

## 4. Enturmação (Vínculo Aluno-Turma)

### Requisitos

- Enturmar um aluno matriculado em uma turma específica.
- Desenturmar (cancelar vínculo) um aluno de uma turma.
- Remanejar um aluno de uma turma para outra.
- Realizar enturmação e cancelamento de enturmação em lote (múltiplos alunos
  de uma vez).
- Bloquear a enturmação em determinado período, de forma configurável.

### Regras de negócio

- Só é possível enturmar um aluno se houver vaga disponível na turma; caso
  contrário, a operação deve ser bloqueada.
- Um aluno não pode estar enturmado simultaneamente em duas turmas com o
  mesmo horário/período.
- A data de enturmação deve estar dentro do intervalo do ano letivo da turma.
- A data de cancelamento da enturmação (desenturmação) deve ser posterior à
  data de enturmação e também estar dentro do ano letivo.
- Enturmar e desenturmar são permissões distintas — um usuário pode ter uma
  sem ter a outra.
- Remanejamento é tratado como uma operação própria (não é apenas
  "desenturmar + enturmar"), com permissão específica.
- Toda operação de enturmação/desenturmação deve ser atômica: se qualquer
  validação falhar, nenhuma alteração parcial deve ser gravada.

**Pontos de atenção para QA:** este é um dos módulos com mais regras
combinadas — priorizar cenários de turma lotada, conflito de horário,
permissões assimétricas (enturmar sem poder desenturmar), e principalmente
teste de atomicidade (forçar falha no meio do processo e verificar que nada
fica gravado parcialmente).

---

## 5. Transferência e Unificação de Cadastros

### Requisitos

- Notificar/processar transferência de aluno entre instituições (integração
  externa).
- Unificar cadastros duplicados de aluno ou de pessoa.
- Desfazer uma unificação já realizada.

### Regras de negócio

- A confirmação de uma transferência externa deve ser validada por token de
  segurança antes de ser processada.
- A unificação de cadastros deve manter um log que permita reverter a
  operação posteriormente.
- Um cadastro unificado (desfeito ou não) deve manter rastreabilidade de qual
  registro foi absorvido por qual.

**Pontos de atenção para QA:** validação de token inválido/expirado na
transferência, teste do fluxo de reversão de unificação, e rastreabilidade
após o desfazimento (o histórico se mantém íntegro?).

---

## 6. Avaliação e Boletim

### Requisitos

- Configurar regras de avaliação por escola/série (tipo de nota, conceito,
  parecer descritivo etc.).
- Configurar fórmula de cálculo de média.
- Configurar tabela de arredondamento de notas.
- Alterar em lote o tipo de boletim aplicado a um conjunto de turmas.
- Definir períodos liberados para lançamento de notas e frequência.

### Regras de negócio

- O lançamento de notas/frequência só é permitido dentro do período liberado
  configurado; fora dele, o lançamento deve ser bloqueado.
- O arredondamento de notas segue a tabela configurada por escola/série, e
  não pode ser ignorado no cálculo final.

**Pontos de atenção para QA:** tentativa de lançamento fora do período
liberado (antes e depois), e verificação matemática do arredondamento em
casos de borda (ex.: 6,95 → 7,0, dependendo da tabela configurada).

---

## 7. Frequência e Situação Final

### Requisitos

- Registrar faltas por aluno, componente curricular e turma.
- Importar em lote a situação final dos alunos (aprovado, reprovado etc.) a
  partir de arquivo externo.
- Atualizar etapa/série em lote.
- Atualizar ano letivo em lote.

### Regras de negócio

- A importação de situação final segue um fluxo obrigatório: envio do
  arquivo → análise dos dados → mapeamento de colunas → confirmação da
  importação → acompanhamento do status do processamento.
- Operações em lote (situação final, ano letivo, série) são processadas de
  forma assíncrona e devem exibir o status do processamento (em andamento,
  concluído, com erro) para o usuário.
- Alterações em lote devem permitir uma etapa de simulação/prévia antes da
  execução definitiva, para o usuário conferir o impacto.

**Pontos de atenção para QA:** cobertura de cada etapa do fluxo de
importação isoladamente (arquivo malformado, mapeamento incorreto de
colunas), teste do status assíncrono (o que acontece se o usuário sair da
tela no meio do processamento?), e conferência de que a prévia realmente
reflete o resultado final antes da execução.

---

## 8. Censo Escolar (Educacenso) e Exportações Oficiais

### Requisitos

- Validar a consistência dos dados cadastrais antes da exportação ao Censo
  Escolar.
- Consultar o resultado de validações e importações do Educacenso.
- Exportar dados no formato exigido pelo SEB (Sistema Estadual/Educacenso).

### Regras de negócio

- A exportação/validação deve identificar e listar pendências cadastrais
  (dados obrigatórios ausentes) antes de permitir a geração final dos dados.
- Alunos, turmas e servidores com cadastro incompleto não devem ser
  considerados aptos para exportação até a correção.

**Pontos de atenção para QA:** simular cadastros incompletos e verificar se
realmente ficam de fora da exportação, e se a lista de pendências é precisa
(sem falso positivo/negativo).

---

## 9. Dispensa de Disciplina

### Requisitos

- Dispensar um aluno de uma disciplina individualmente.
- Dispensar alunos de disciplina em lote.
- Consultar a lista de dispensas concedidas.

### Regras de negócio

- A dispensa deve ficar vinculada a um motivo/justificativa.
- Uma dispensa não pode ser concedida em duplicidade para o mesmo
  aluno/disciplina/período.

**Pontos de atenção para QA:** tentativa de dispensa duplicada (individual e
em lote), e obrigatoriedade real do campo de justificativa.

---

## 10. Comunicação (Avisos e Notificações)

### Requisitos

- Publicar avisos/comunicados para usuários do sistema.
- Editar avisos já publicados.
- Exibir ao usuário os avisos pendentes de leitura.
- Confirmar a leitura de um aviso.
- Listar notificações do usuário logado e a quantidade de não lidas.
- Marcar notificações como lidas (individualmente ou todas de uma vez).

### Regras de negócio

- Um usuário pode ser impedido de continuar navegando no sistema até
  confirmar a leitura de um aviso obrigatório.
- A contagem de notificações não lidas deve refletir em tempo real as ações
  de leitura do usuário.

**Pontos de atenção para QA:** bloqueio de navegação até leitura obrigatória
(tentar contornar via URL direta), e consistência do contador de não lidas
após marcar como lida individualmente vs. em lote.

---

## 11. Configurações e Administração

### Requisitos

- Configurar parâmetros gerais do sistema por instituição.
- Realizar backup dos dados (download).
- Exportar dados gerais do sistema.
- Exportar documentos/arquivos.
- Gerenciar componentes curriculares em lote (cursos, séries, componentes),
  com prévia antes da execução.
- Cadastrar, editar e excluir tipos/perfis de usuário e suas permissões.

### Regras de negócio

- Cada operação administrativa sensível (exportação de dados, backup,
  alteração de perfis) exige permissão específica, independente do perfil
  geral do usuário.
- Alterações em lote de componentes devem passar por uma etapa de prévia
  (preview) antes da execução definitiva, assim como as demais operações em
  massa do sistema.

**Pontos de atenção para QA:** usuário com perfil "admin geral" mas sem a
permissão específica de exportação/backup — a operação deve ser bloqueada
mesmo assim.

---

## 12. Upload e Gestão de Arquivos

### Requisitos

- Permitir o upload de arquivos vinculados a diversos processos do sistema
  (documentos, importações, exportações).
- Permitir a visualização/download de um arquivo previamente enviado.

### Regras de negócio

- Cada arquivo enviado deve ficar associado a um identificador único,
  permitindo sua recuperação posterior por outros módulos.

**Pontos de atenção para QA:** unicidade do identificador sob concorrência
(dois uploads simultâneos), e recuperação do arquivo por diferentes módulos
que o referenciam.

---

## 13. Requisitos não funcionais gerais

- O sistema deve manter histórico/trilha de auditoria para operações
  críticas (matrícula, unificação de cadastros, situação final).
- Operações em lote e importações devem ser processadas em segundo plano,
  sem travar a navegação do usuário, com tela de acompanhamento de status.
- O sistema deve impedir a execução parcial de operações compostas (ex.: se
  uma etapa de uma enturmação falhar, nada deve ser gravado).
- O sistema deve ser compatível com o uso simultâneo por múltiplas
  instituições, mantendo isolamento total dos dados entre elas.
- O sistema deve possuir proteções básicas de segurança web (contra XSS,
  CSRF e exibição indevida em iframes de terceiros).

**Pontos de atenção para QA:** estes itens cruzam vários módulos — vale
tratá-los como checklist transversal (auditoria, atomicidade, isolamento
multi-instituição e segurança básica) aplicado a cada funcionalidade testada,
não como um módulo isolado.

---

## Como usar este documento

Este arquivo serve como insumo para:

- Priorização de casos de teste (`CT-NNN`) por módulo, com foco maior nos
  módulos com mais regras combinadas (Enturmação, Frequência/Situação Final);
- Definição de cenários de teste exploratório;
- Checklist de regressão para operações em lote e assíncronas;
- Base para reportar bugs (`BUG-AAAAMMDD-NNN`) e melhorias
  (`MELHORIA-AAAAMMDD-NNN`) com referência clara à regra de negócio violada.
