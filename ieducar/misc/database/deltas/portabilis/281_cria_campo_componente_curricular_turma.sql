-- Cria campo para controlar a inserção de componentes curriculares na turma
-- @author Caroline Salib <caroline@portabilis.com.br>

ALTER TABLE pmieducar.instituicao ADD COLUMN componente_curricular_turma boolean;

-- undo

ALTER TABLE pmieducar.instituicao DROP COLUMN componente_curricular_turma;
