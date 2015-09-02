-- //

--
-- Altera tamanho do campo setando limite para 60 caracteres
--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE modules.area_conhecimento ALTER COLUMN nome type character varying(60);

-- //@UNDO

ALTER TABLE modules.area_conhecimento ALTER COLUMN nome type character varying(40);

-- //