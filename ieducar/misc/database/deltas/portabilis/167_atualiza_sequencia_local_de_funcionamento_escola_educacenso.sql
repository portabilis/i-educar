--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

UPDATE pmieducar.escola
SET local_funcionamento = local_funcionamento - 3
WHERE local_funcionamento >= 7;