  -- //

  --
  -- Adiciona colunas referente a alterações em processos de deslocamento de alunos para atender PRODESP
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.matricula_turma ADD COLUMN transferido boolean;
  ALTER TABLE pmieducar.matricula_turma ADD COLUMN remanejado boolean;
  ALTER TABLE pmieducar.instituicao ADD COLUMN data_base_remanejamento date;
  ALTER TABLE pmieducar.instituicao ADD COLUMN data_base_transferencia date;

  -- //@UNDO

  ALTER TABLE pmieducar.matricula_turma DROP COLUMN transferido;
  ALTER TABLE pmieducar.matricula_turma DROP COLUMN remanejado;
  ALTER TABLE pmieducar.instituicao DROP COLUMN data_base_remanejamento;
  ALTER TABLE pmieducar.instituicao DROP COLUMN data_base_transferencia;

  -- //
