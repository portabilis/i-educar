  -- //

  --
  -- Cria campo código sistema em pmieducar.aluno
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
  ALTER TABLE pmieducar.aluno ADD COLUMN codigo_sistema CHARACTER VARYING(30);

  -- //@UNDO
 
  ALTER TABLE pmieducar.aluno DROP COLUMN codigo_sistema;

  -- //