CREATE OR REPLACE view public.exporter_employee as
SELECT DISTINCT p.*,
                to_char(floor(servidor.carga_horaria), 'FM999999') || ':' ||
                to_char((servidor.carga_horaria - floor(servidor.carga_horaria)) * 60, 'FM00') AS employee_workload,
                servidor.cod_servidor,
                employee_graduation.complete                                                   AS employee_graduation_complete,
                escolaridade.descricao                                                         AS schooling_degree,
                employee_postgraduates.complete                                                AS employee_postgraduates_complete,
                CASE servidor.tipo_ensino_medio_cursado
                    WHEN 1 THEN 'Formação Geral'
                    WHEN 2 THEN 'Modalidade Normal (Magistério)'
                    WHEN 3 THEN 'Curso Técnico'
                    WHEN 4 THEN 'Magistério Indígena Modalidade Normal'
                    ELSE ''
                    END                                                                        AS high_school_type,
                form.continuing_education_course,
                employee_alocations.role                                                       AS role,
                employee_alocations.link                                                       AS link,
                employee_alocations.year                                                       AS year,
                employee_alocations.year_id                                                    AS year_id,
                employee_alocations.worload                                                    AS allocated_workload,
                employee_alocations.period                                                     AS period,
                employee_alocations.school                                                     AS school,
                employee_alocations.school_id                                                  AS school_id
FROM public.exporter_person p
         JOIN pmieducar.servidor servidor ON p.id = servidor.cod_servidor
         LEFT JOIN cadastro.escolaridade
                   ON escolaridade.idesco = servidor.ref_idesco,
     LATERAL (
         SELECT string_agg(distinct f.nm_funcao, ', ')               AS role,
                string_agg(distinct fv.nm_vinculo, ', ')             AS link,
                array_agg(distinct sa.ano)                           AS year_id,
                string_agg(distinct sa.ano::varchar, ', ')           AS year,
                string_agg(distinct sa.carga_horaria::varchar, ', ') AS worload,
                string_agg(distinct (CASE sa.periodo
                                         WHEN 1 THEN 'Matutino'
                                         WHEN 2 THEN 'Vespertino'
                                         WHEN 3 THEN 'Noturno'
                                         ELSE ''
                    END), ', ')                                      AS period,
                string_agg(distinct ep.nome, ', ')                   AS school,
                array_agg(distinct e.cod_escola)                     AS school_id
         FROM pmieducar.servidor_alocacao sa
                  LEFT JOIN pmieducar.escola e ON e.cod_escola = sa.ref_cod_escola
                  LEFT JOIN cadastro.pessoa ep ON ep.idpes = e.ref_idpes
                  LEFT JOIN pmieducar.servidor_funcao sf
                            ON sf.ref_cod_servidor = sa.ref_cod_servidor AND
                               sf.cod_servidor_funcao = sa.ref_cod_servidor_funcao
                  LEFT JOIN pmieducar.funcao f ON f.cod_funcao = sf.ref_cod_funcao
                  LEFT JOIN portal.funcionario_vinculo fv ON fv.cod_funcionario_vinculo = sa.ref_cod_funcionario_vinculo
         WHERE servidor.cod_servidor = sa.ref_cod_servidor
         ) AS employee_alocations,
     LATERAL (
         SELECT CONCAT_WS(', ',
                          CASE
                              WHEN (ARRAY [1] <@ scfc.curso_formacao_continuada)::bool THEN 'Creche (0 a 3 anos)'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [2] <@ scfc.curso_formacao_continuada)::bool THEN 'Pré-escola (4 e 5 anos)'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [3] <@ scfc.curso_formacao_continuada)::bool
                                  THEN 'Anos iniciais do ensino fundamental'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [4] <@ scfc.curso_formacao_continuada)::bool
                                  THEN 'Anos finais do ensino fundamental'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [5] <@ scfc.curso_formacao_continuada)::bool THEN 'Ensino médio'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [6] <@ scfc.curso_formacao_continuada)::bool
                                  THEN 'Educação de jovens e adultos'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [7] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação especial'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [8] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação indígena'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [9] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação do campo'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [10] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação ambiental'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [11] <@ scfc.curso_formacao_continuada)::bool
                                  THEN 'Educação em direitos humanos'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [12] <@ scfc.curso_formacao_continuada)::bool
                                  THEN 'Gênero e diversidade sexual'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [13] <@ scfc.curso_formacao_continuada)::bool
                                  THEN 'Direitos de criança e adolescente'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [14] <@ scfc.curso_formacao_continuada)::bool
                                  THEN 'Educação para as relações étnico-raciais e História e cultura Afro-Brasileira e Africana'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [17] <@ scfc.curso_formacao_continuada)::bool THEN 'Gestão Escolar'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [15] <@ scfc.curso_formacao_continuada)::bool THEN 'Outros'::VARCHAR
                              ELSE NULL::VARCHAR END,
                          CASE
                              WHEN (ARRAY [16] <@ scfc.curso_formacao_continuada)::bool THEN 'Nenhum'::VARCHAR
                              ELSE NULL::VARCHAR END
                    )
                    AS continuing_education_course
         FROM pmieducar.servidor as scfc
         WHERE scfc.cod_servidor = servidor.cod_servidor
         ) form,
     LATERAL (
         SELECT STRING_AGG(('[' || CONCAT_WS(', ', educacenso_curso_superior.nome, completion_year, educacenso_ies.nome,
                                             employee_graduation_disciplines.name) || ']')::varchar, ';') as complete
         FROM employee_graduations
                  JOIN modules.educacenso_curso_superior
                       ON educacenso_curso_superior.id = employee_graduations.course_id
                  JOIN modules.educacenso_ies ON educacenso_ies.id = employee_graduations.college_id
                  LEFT JOIN employee_graduation_disciplines
                            ON employee_graduations.discipline_id = employee_graduation_disciplines.id
         WHERE employee_graduations.employee_id = servidor.cod_servidor
         ) AS employee_graduation,
     LATERAL (
         SELECT CONCAT_WS(', ',
                          CASE WHEN epg.type_id = 1 THEN 'Especialização' ELSE NULL::VARCHAR END,
                          CASE WHEN epg.type_id = 2 THEN 'Mestrado' ELSE NULL::VARCHAR END,
                          CASE WHEN epg.type_id = 3 THEN 'Doutorado' ELSE NULL::VARCHAR END,
                          CASE WHEN epg.type_id = 4 THEN 'Não tem pós-graduação concluída' ELSE NULL::VARCHAR END
                    )
                    AS complete
         FROM pmieducar.servidor as serv
                  LEFT JOIN employee_posgraduate as epg ON epg.employee_id = serv.cod_servidor
         WHERE servidor.cod_servidor = serv.cod_servidor
         ) AS employee_postgraduates
ORDER BY p.name;
