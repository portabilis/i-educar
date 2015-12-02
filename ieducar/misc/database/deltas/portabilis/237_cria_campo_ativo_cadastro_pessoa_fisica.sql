--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE cadastro.fisica ADD COLUMN ativo INT DEFAULT 1;

-- undo

ALTER TABLE cadastro.fisica DROP COLUMN ativo;