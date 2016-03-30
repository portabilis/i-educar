-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
-- Muda o nome da coluna area_específica

ALTER TABLE modules.professor_turma RENAME COLUMN area_especifica TO permite_lancar_faltas_componente;

--@UNDO

ALTER TABLE modules.professor_turma RENAME COLUMN permite_lancar_faltas_componente TO area_especifica;
