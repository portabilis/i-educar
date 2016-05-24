-- Cria campo para controlar a inserção de componentes curriculares na turma
-- @author Caroline Salib <caroline@portabilis.com.br>

ALTER TABLE pmieducar.instituicao ADD COLUMN componente_curricular_turma boolean;

UPDATE pmieducar.instituicao SET componente_curricular_turma = TRUE WHERE cod_instituicao > 0;

-- undo

ALTER TABLE pmieducar.instituicao DROP COLUMN componente_curricular_turma;
