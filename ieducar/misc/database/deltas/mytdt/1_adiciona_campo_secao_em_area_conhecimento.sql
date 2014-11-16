-- //

--
-- Adiciona o campo secao na tabela area_conhecimento
--
-- @author   Thieres Tembra <tdt@mytdt.com.br>
-- @license  @@license@@
-- @version  $Id$
--

  ALTER TABLE modules.area_conhecimento ADD COLUMN secao character varying(50);

-- //@UNDO

  ALTER TABLE modules.area_conhecimento DROP COLUMN secao;

-- //
