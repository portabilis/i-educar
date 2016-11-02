 -- //

  --
  -- Cria coluna para data de enturmação
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.matricula_turma ALTER COLUMN data_enturmacao SET NOT NULL; 

  ALTER TABLE pmieducar.matricula_turma 
  add COLUMN sequencial_fechamento INTEGER NOT NULL default 0;  

  ALTER TABLE pmieducar.turma 
  add COLUMN data_fechamento date;

  -- //@UNDO
  
  ALTER TABLE pmieducar.matricula_turma DROP COLUMN sequencial_fechamento;
  ALTER TABLE pmieducar.turma DROP COLUMN data_fechamento;

  -- //