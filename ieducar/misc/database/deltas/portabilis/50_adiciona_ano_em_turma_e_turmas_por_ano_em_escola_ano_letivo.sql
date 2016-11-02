  -- //

  --
  -- Adiciona ano em em pmieducar.turma e turmas_por_ano em pmieducar.escola_ano_letivo
  -- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.turma ADD COLUMN ano integer;
  ALTER TABLE pmieducar.escola_ano_letivo ADD COLUMN turmas_por_ano smallint;

  -- //@UNDO

  ALTER TABLE pmieducar.turma DROP COLUMN ano;
  ALTER TABLE pmieducar.escola_ano_letivo DROP COLUMN turmas_por_ano;

  -- //
