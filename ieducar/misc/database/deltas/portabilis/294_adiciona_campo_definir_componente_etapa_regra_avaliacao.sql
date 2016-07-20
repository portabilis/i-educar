-- Adiciona campo definir_componente_etapa na tabela de regra de avaliação
-- @author Caroline Salib <caroline@portabilis.com.br>

ALTER TABLE modules.regra_avaliacao ADD COLUMN definir_componente_etapa SMALLINT;

UPDATE modules.regra_avaliacao SET definir_componente_etapa = 0 WHERE id > 0;