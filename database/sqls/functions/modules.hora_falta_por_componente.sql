CREATE OR REPLACE FUNCTION modules.hora_falta_por_componente(cod_matricula_id integer, cod_disciplina_id integer)
RETURNS character varying
LANGUAGE plpgsql
AS $$
DECLARE
    v_hora_falta float;
    cod_serie_id integer;
    cod_escola_id integer;
BEGIN

	cod_escola_id := (
        SELECT ref_ref_cod_escola
        FROM pmieducar.matricula
        WHERE cod_matricula = cod_matricula_id
    );

    cod_serie_id := (
        SELECT ref_ref_cod_serie
        FROM pmieducar.matricula
        WHERE cod_matricula = cod_matricula_id
    );

    v_hora_falta := (
        SELECT hora_falta :: float
        FROM pmieducar.escola_serie_disciplina
        WHERE escola_serie_disciplina.ref_cod_disciplina = cod_disciplina_id
        AND escola_serie_disciplina.ref_ref_cod_serie = cod_serie_id
        AND escola_serie_disciplina.ref_ref_cod_escola = cod_escola_id
    );

    IF (v_hora_falta IS NULL) THEN
        v_hora_falta := (
            SELECT hora_falta :: float
            FROM modules.componente_curricular_ano_escolar
            WHERE componente_curricular_ano_escolar.componente_curricular_id = cod_disciplina_id
            AND componente_curricular_ano_escolar.ano_escolar_id = cod_serie_id
        );
    END IF;


    IF (v_hora_falta IS NULL) THEN
        v_hora_falta := (
            SELECT hora_falta
            FROM pmieducar.curso c
                     INNER JOIN pmieducar.matricula m ON (c.cod_curso = m.ref_cod_curso)
            WHERE m.cod_matricula = cod_matricula_id
        );
    END IF;

    RETURN  v_hora_falta;

END;
$$;
