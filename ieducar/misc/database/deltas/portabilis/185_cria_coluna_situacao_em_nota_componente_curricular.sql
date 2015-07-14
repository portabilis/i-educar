--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE modules.nota_componente_curricular ADD COLUMN situacao integer;

  -- undo

ALTER TABLE modules.nota_componente_curricular DROP COLUMN situacao;