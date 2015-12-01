  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

ALTER TABLE modules.formula_media ALTER COLUMN formula_media TYPE varchar(200);

-- UNDO

ALTER TABLE modules.formula_media ALTER COLUMN formula_media TYPE varchar(50);