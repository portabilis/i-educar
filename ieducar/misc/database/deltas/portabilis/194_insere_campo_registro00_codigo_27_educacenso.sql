--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.escola ADD COLUMN orgao_regional INTEGER;

-- UNDO

ALTER TABLE pmieducar.escola DROP COLUMN orgao_regional;