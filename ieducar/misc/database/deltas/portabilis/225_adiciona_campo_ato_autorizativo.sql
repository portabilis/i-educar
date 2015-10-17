--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


 ALTER TABLE pmieducar.escola ADD COLUMN ato_autorizativo VARCHAR(255);

 --undo

 ALTER TABLE pmieducar.escola DROP COLUMN ato_autorizativo;
