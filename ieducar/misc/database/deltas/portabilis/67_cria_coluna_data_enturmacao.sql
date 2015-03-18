 -- //

  --
  -- Cria coluna para data de enturmação
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.matricula_turma 
  add COLUMN data_enturmacao date;

  UPDATE pmieducar.matricula_turma set data_enturmacao = data_cadastro;

  -- //@UNDO

  ALTER TABLE pmieducar.matricula_turma DROP COLUMN data_enturmacao;

  -- //