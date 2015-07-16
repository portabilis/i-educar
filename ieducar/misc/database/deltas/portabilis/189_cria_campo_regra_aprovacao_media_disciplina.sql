--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


ALTER TABLE modules.regra_avaliacao ADD COLUMN aprova_media_disciplina SMALLINT DEFAULT 0;

-- UNDO

ALTER TABLE modules.regra_avaliacao ADD COLUMN aprova_media_disciplina;