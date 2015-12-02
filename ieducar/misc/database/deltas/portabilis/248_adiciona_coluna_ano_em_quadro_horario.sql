--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.quadro_horario ADD COLUMN ano INTEGER;

-- undo

ALTER TABLE pmieducar.quadro_horario DROP COLUMN ano;