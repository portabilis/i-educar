--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.aluno ALTER COLUMN url_laudo_medico TYPE text;

  -- undo

ALTER TABLE pmieducar.aluno ALTER COLUMN url_laudo_medico TYPE varchar(255);