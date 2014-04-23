  -- //

  --
  -- Remove a obrigatoriedade do campo intervalo no cadastro de série
  --
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.serie ALTER COLUMN intervalo DROP NOT NULL;

  -- //@UNDO

  UPDATE pmieducar.serie SET intervalo = 0 WHERE intervalo IS NULL;
  ALTER TABLE PMIEDUCAR.SERIE ALTER COLUMN INTERVALO SET NOT NULL;

  -- //