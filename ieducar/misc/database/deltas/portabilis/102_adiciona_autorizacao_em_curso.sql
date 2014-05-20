  -- //

  --
  -- Adiciona campo autorização no curso
  --
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
  ALTER TABLE pmieducar.curso ADD autorizacao VARCHAR(250)

  -- //@UNDO
  
  ALTER TABLE pmieducar.curso DROP autorizacao
    
  -- //