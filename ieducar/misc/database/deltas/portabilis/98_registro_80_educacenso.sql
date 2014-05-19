  -- //

  --
  -- Cria colunas necessárias para atender o registro 80 do Educacenso
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.turma ADD COLUMN turma_unificada SMALLINT;

  ALTER TABLE pmieducar.turma ADD COLUMN etapa_educacenso SMALLINT;

  -- //@UNDO
  
  ALTER TABLE pmieducar.turma DROP COLUMN turma_unificada;

  ALTER TABLE pmieducar.turma DROP COLUMN etapa_educacenso;

  -- //