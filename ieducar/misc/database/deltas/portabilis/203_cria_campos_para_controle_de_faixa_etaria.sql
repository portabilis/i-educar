--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.instituicao ADD COLUMN data_base_matricula date;

ALTER TABLE pmieducar.serie ADD COLUMN alerta_faixa_etaria bool;
ALTER TABLE pmieducar.serie ADD COLUMN bloquear_matricula_faixa_etaria bool;

  -- undo

ALTER TABLE pmieducar.instituicao DROP COLUMN data_base_matricula;

ALTER TABLE pmieducar.serie DROP COLUMN alerta_faixa_etaria;
ALTER TABLE pmieducar.serie DROP COLUMN bloquear_matricula_faixa_etaria;