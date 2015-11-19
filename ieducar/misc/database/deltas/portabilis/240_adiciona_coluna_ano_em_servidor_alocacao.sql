--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


ALTER TABLE pmieducar.servidor_alocacao ADD COLUMN ano INTEGER;

-- undo

ALTER TABLE pmieducar.servidor_alocacao DROP COLUMN ano;