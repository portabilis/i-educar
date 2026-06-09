CREATE OR REPLACE FUNCTION modules.frequencia_da_matricula(p_matricula_id integer)
    RETURNS double precision
    LANGUAGE plpgsql
AS $function$
DECLARE
    v_regra_falta integer;
    v_falta_aluno_id  integer;
    v_qtd_dias_letivos_serie NUMERIC;
    v_total_faltas integer;
    v_qtd_horas_serie integer;
    v_total_hora_falta FLOAT;
BEGIN
    /*
        v_regra_falta:
        1- Global
        2- Por componente
    */
    v_regra_falta:= (
        SELECT regra_avaliacao.tipo_presenca
        FROM pmieducar.matricula
                 INNER JOIN pmieducar.serie
                            ON serie.cod_serie = matricula.ref_ref_cod_serie
                 INNER JOIN modules.regra_avaliacao_serie_ano rasa
                            ON serie.cod_serie = rasa.serie_id
                                AND rasa.ano_letivo = matricula.ano
                 INNER JOIN modules.regra_avaliacao
                            ON regra_avaliacao.id = rasa.regra_avaliacao_id
        WHERE matricula.cod_matricula = p_matricula_id
    );
    v_falta_aluno_id := (
        SELECT id
        FROM modules.falta_aluno
        WHERE matricula_id = p_matricula_id
        ORDER BY id DESC
        LIMIT 1
    );
    IF (v_regra_falta = 1) THEN
        v_qtd_dias_letivos_serie := (
            SELECT s.dias_letivos
            FROM pmieducar.serie s
                     INNER JOIN pmieducar.matricula m
                                ON (m.ref_ref_cod_serie = s.cod_serie)
            WHERE m.cod_matricula = p_matricula_id
        );
        v_total_faltas := (
            SELECT SUM(quantidade)
            FROM modules.falta_geral
            WHERE falta_aluno_id = v_falta_aluno_id
        );

        RETURN TRUNC((((v_qtd_dias_letivos_serie - v_total_faltas) * 100 ) / v_qtd_dias_letivos_serie )::numeric,1);
    ELSE

        v_qtd_horas_serie := (
            SELECT s.carga_horaria
            FROM pmieducar.serie s
                     INNER JOIN pmieducar.matricula m
                                ON (m.ref_ref_cod_serie = s.cod_serie)
            WHERE m.cod_matricula = p_matricula_id
        );

        v_total_hora_falta := (
            /*
               Soma todos so sub_totais que foram calculados individualmente
             */
            SELECT sum(sub_totais.totais) from (
                SELECT
                    /*
                        Calcula para cada componente curricular seu total de horas faltas
                        com base na carga horaria do componente.
                        Foi aplicado o (* 100) que estava no retorno da função no retor na quantidade de horas falta
                        do componente curricular
                    */
                    SUM(fcc.quantidade) * (modules.hora_falta_por_componente(p_matricula_id, fcc.componente_curricular_id)::float * 100)::float as "totais"
                    FROM modules.falta_componente_curricular fcc
                    WHERE fcc.falta_aluno_id = v_falta_aluno_id
                    GROUP BY fcc.componente_curricular_id
                ) as sub_totais
        );

        RETURN  TRUNC((100 - ( v_total_hora_falta / v_qtd_horas_serie))::numeric, 1);
    END IF;
END;
$function$;
