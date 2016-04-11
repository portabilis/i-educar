-- @author   Paula Bonot <bonot@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
-- Remove constraint idpes da tabela cliente

ALTER TABLE pmieducar.cliente DROP CONSTRAINT cliente_ref_idpes_ukey;