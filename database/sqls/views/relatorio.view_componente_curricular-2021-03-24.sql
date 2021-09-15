CREATE OR REPLACE VIEW relatorio.view_componente_curricular AS
(
    SELECT
		cc.id,
        t.cod_turma,
		coalesce(ts.serie_id, t.ref_ref_cod_serie) AS cod_serie,
        cc.nome,
        cc.abreviatura,
        cc.ordenamento,
        cc.area_conhecimento_id,
        cc.tipo_base,
        esd.etapas_especificas,
        esd.etapas_utilizadas,
        coalesce(cct.carga_horaria, esd.carga_horaria, ccae.carga_horaria) AS carga_horaria
    FROM pmieducar.turma t
    LEFT JOIN pmieducar.turma_serie ts ON ts.turma_id = t.cod_turma
    JOIN pmieducar.escola_serie es ON (
        es.ref_cod_escola = t.ref_ref_cod_escola
        AND es.ref_cod_serie = coalesce(ts.serie_id, t.ref_ref_cod_serie)
    )
    JOIN pmieducar.escola_serie_disciplina esd ON (
        esd.ref_ref_cod_escola = es.ref_cod_escola
        AND esd.ref_ref_cod_serie = es.ref_cod_serie
    )
    JOIN modules.componente_curricular_ano_escolar ccae ON (
        ccae.ano_escolar_id = es.ref_cod_serie
        AND ccae.componente_curricular_id = esd.ref_cod_disciplina
    )
    JOIN modules.componente_curricular cc ON cc.id = ccae.componente_curricular_id
    LEFT JOIN modules.componente_curricular_turma cct ON (
        cct.turma_id = t.cod_turma
        AND cct.componente_curricular_id = cc.id
    )
    WHERE CASE
        WHEN EXISTS (
            SELECT 1
            FROM modules.componente_curricular_turma
            WHERE componente_curricular_turma.turma_id = t.cod_turma
        ) THEN cct.turma_id IS NOT NULL
        ELSE true
    END
);
