--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE modules.regra_avaliacao ADD COLUMN qtd_disciplinas_dependencia SMALLINT NOT NULL DEFAULT 0;