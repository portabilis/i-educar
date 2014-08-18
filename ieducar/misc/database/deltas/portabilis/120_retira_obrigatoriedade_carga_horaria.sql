  -- //
  -- Essa migração cria tabela para bloqueio do ano letivo
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  ALTER TABLE pmieducar.historico_escolar ALTER COLUMN carga_horaria DROP NOT NULL;

  -- //@UNDO

  UPDATE pmieducar.historico_escolar set carga_horaria = 0 WHERE carga_horaria IS NULL;
  ALTER TABLE pmieducar.historico_escolar ALTER COLUMN carga_horaria SET NOT NULL;

  -- //