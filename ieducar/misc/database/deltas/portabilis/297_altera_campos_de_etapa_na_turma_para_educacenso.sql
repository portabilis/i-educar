-- Altera campos de etapa na turma referente ao educacenso
-- @author Caroline Salib <caroline@portabilis.com.br>

-- Remove campo etapa_id pois o mesmo não é utilizado e causa dúvida de implementação
ALTER TABLE pmieducar.turma DROP COLUMN etapa_id;

-- Adiciona campo auxiliar para etapa de ensino informado no educacenso
ALTER TABLE pmieducar.turma ADD COLUMN etapa_educacenso2 SMALLINT;