--
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

DROP VIEW relatorio.view_componente_curricular;

ALTER TABLE modules.area_conhecimento ALTER COLUMN nome TYPE varchar(200);


CREATE OR REPLACE VIEW relatorio.view_componente_curricular AS
 (SELECT escola_serie_disciplina.ref_cod_disciplina AS id,
  turma.cod_turma, componente_curricular.nome,
  componente_curricular.abreviatura

   FROM pmieducar.turma
   JOIN pmieducar.escola_serie_disciplina ON escola_serie_disciplina.ref_ref_cod_serie = turma.ref_ref_cod_serie AND escola_serie_disciplina.ref_ref_cod_escola = turma.ref_ref_cod_escola
   JOIN modules.componente_curricular ON componente_curricular.id = escola_serie_disciplina.ref_cod_disciplina
   AND (( SELECT count(cct.componente_curricular_id) AS count

   FROM modules.componente_curricular_turma cct
  WHERE cct.turma_id = turma.cod_turma)) = 0
   JOIN modules.area_conhecimento ON area_conhecimento.id = componente_curricular.area_conhecimento_id
   ORDER BY area_conhecimento.ordenamento_ac, area_conhecimento.nome, componente_curricular.ordenamento, componente_curricular.nome)
UNION ALL
 (SELECT componente_curricular_turma.componente_curricular_id AS id, componente_curricular_turma.turma_id AS cod_turma, componente_curricular.nome, componente_curricular.abreviatura
   FROM modules.componente_curricular_turma
   JOIN modules.componente_curricular ON componente_curricular.id = componente_curricular_turma.componente_curricular_id
   JOIN modules.area_conhecimento ON area_conhecimento.id = componente_curricular.area_conhecimento_id
  ORDER BY area_conhecimento.ordenamento_ac, area_conhecimento.nome, componente_curricular.ordenamento, componente_curricular.nome)

  -- undo

  ALTER TABLE modules.area_conhecimento ALTER COLUMN nome TYPE varchar(60);