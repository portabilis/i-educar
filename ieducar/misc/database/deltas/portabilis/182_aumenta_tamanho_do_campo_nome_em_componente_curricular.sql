--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


ALTER TABLE modules.componente_curricular ALTER COLUMN nome TYPE varchar(500);

  -- undo

ALTER TABLE modules.componente_curricular ALTER COLUMN nome TYPE varchar(200);