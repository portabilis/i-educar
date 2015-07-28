--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.candidato_reserva_vaga
  ADD COLUMN ref_cod_escola integer;
ALTER TABLE pmieducar.candidato_reserva_vaga
  ADD CONSTRAINT candidato_reserva_vaga_ref_cod_escola FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola (cod_escola) ON UPDATE NO ACTION ON DELETE NO ACTION;

  -- undo

ALTER TABLE pmieducar.candidato_reserva_vaga DROP CONSTRAINT candidato_reserva_vaga_ref_cod_escola;
ALTER TABLE pmieducar.candidato_reserva_vaga DROP COLUMN ref_cod_escola;