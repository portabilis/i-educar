--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE modules.nota_componente_curricular_media ADD COLUMN situacao integer;

  -- undo

ALTER TABLE modules.nota_componente_curricular_media DROP COLUMN situacao;