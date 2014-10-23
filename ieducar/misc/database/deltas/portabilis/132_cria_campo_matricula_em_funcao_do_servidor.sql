  --
  -- Cria coluna matricula em função
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.servidor_funcao ADD COLUMN matricula VARCHAR;

  -- //@UNDO

  ALTER TABLE pmieducar.servidor_funcao DROP matricula;

  -- //