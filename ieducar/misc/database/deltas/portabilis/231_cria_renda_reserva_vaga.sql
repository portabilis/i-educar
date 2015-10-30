--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.instituicao ADD COLUMN reserva_integral_somente_com_renda BOOLEAN NOT NULL DEFAULT FALSE;