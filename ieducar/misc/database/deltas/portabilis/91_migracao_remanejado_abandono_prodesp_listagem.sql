  -- //

  --
  -- Adiciona colunas referente a alterações em processos de remanejamento e abandono de alunos para atender PRODESP
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.matricula_turma ADD COLUMN reclassificado boolean;
  ALTER TABLE pmieducar.matricula_turma ADD COLUMN abandono boolean;

  -- //@UNDO

  ALTER TABLE pmieducar.matricula_turma DROP COLUMN reclassificado;
  ALTER TABLE pmieducar.matricula_turma DROP COLUMN abandono;

  -- //
