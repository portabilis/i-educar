CREATE OR REPLACE VIEW relatorio.view_componente_curricular AS
(
    SELECT
        escola_serie_disciplina.ref_cod_disciplina AS id,
        turma.cod_turma,
        componente_curricular.nome,
        componente_curricular.abreviatura,
        componente_curricular.ordenamento,
        componente_curricular.area_conhecimento_id,
        componente_curricular.tipo_base,
        escola_serie_disciplina.etapas_especificas,
        escola_serie_disciplina.etapas_utilizadas,
        escola_serie_disciplina.carga_horaria
    FROM pmieducar.turma
    JOIN pmieducar.escola_serie_disciplina
    ON escola_serie_disciplina.ref_ref_cod_serie = turma.ref_ref_cod_serie
    AND escola_serie_disciplina.ref_ref_cod_escola = turma.ref_ref_cod_escola
    AND escola_serie_disciplina.ativo = 1
    AND turma.ano = ANY (escola_serie_disciplina.anos_letivos)
    JOIN modules.componente_curricular
    ON componente_curricular.id = escola_serie_disciplina.ref_cod_disciplina
    AND (
        SELECT count(cct.componente_curricular_id) AS count
        FROM modules.componente_curricular_turma cct
        WHERE cct.turma_id = turma.cod_turma
    ) = 0
    JOIN modules.area_conhecimento
    ON area_conhecimento.id = componente_curricular.area_conhecimento_id
    ORDER BY
        area_conhecimento.ordenamento_ac,
        area_conhecimento.nome,
        componente_curricular.ordenamento,
        componente_curricular.nome
)
UNION ALL
(
    SELECT
        componente_curricular_turma.componente_curricular_id AS id,
        componente_curricular_turma.turma_id AS cod_turma,
        componente_curricular.nome,
        componente_curricular.abreviatura,
        componente_curricular.ordenamento,
        componente_curricular.area_conhecimento_id,
        componente_curricular.tipo_base,
        componente_curricular_turma.etapas_especificas,
        componente_curricular_turma.etapas_utilizadas,
        componente_curricular_turma.carga_horaria
    FROM modules.componente_curricular_turma
    JOIN modules.componente_curricular
    ON componente_curricular.id = componente_curricular_turma.componente_curricular_id
    JOIN modules.area_conhecimento
    ON area_conhecimento.id = componente_curricular.area_conhecimento_id
    ORDER BY
        area_conhecimento.ordenamento_ac,
        area_conhecimento.nome,
        componente_curricular.ordenamento,
        componente_curricular.nome
);
