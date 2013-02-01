  -- //

  --
  -- Adiciona tipo_boletim em pmieducar.turma
  -- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.aluno ADD COLUMN aluno_estado_id integer;

  -- //@UNDO

  ALTER TABLE pmieducar.aluno DROP COLUMN aluno_estado_id;

  -- //
