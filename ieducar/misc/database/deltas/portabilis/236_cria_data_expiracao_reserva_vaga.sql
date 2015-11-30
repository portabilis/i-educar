--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.instituicao ADD COLUMN data_expiracao_reserva_vaga date;

-- //@UNDO

ALTER TABLE pmieducar.instituicao DROP COLUMN data_expiracao_reserva_vaga;