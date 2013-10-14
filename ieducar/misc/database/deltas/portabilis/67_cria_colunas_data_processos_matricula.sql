  -- //

  --
  -- Cria colunas para armazenar data dos processos da matrícula
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.matricula ADD COLUMN data_matricula timestamp without time zone;
  ALTER TABLE pmieducar.matricula ADD COLUMN data_cancel timestamp without time zone;
  
  -- //@UNDO

  ALTER TABLE pmieducar.matricula DROP COLUMN data_matricula;
  ALTER TABLE pmieducar.matricula DROP COLUMN data_cancel;

  -- //
