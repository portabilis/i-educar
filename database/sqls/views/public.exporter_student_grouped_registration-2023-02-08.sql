create view public.exporter_student_grouped_registration as
SELECT p.id,
       p.name,
       p.social_name,
       string_agg(t.nm_turma, '|') AS school_class,
       string_agg(s.nm_serie, '|') AS grade,
       string_agg(c.nm_curso, '|') AS course,
       p.cpf,
       p.rg,
       p.rg_issue_date,
       p.rg_state_abbreviation,
       p.date_of_birth,
       p.email,
       p.sus,
       p.nis,
       p.occupation,
       p.organization,
       p.monthly_income,
       p.gender,
       p.race,
       p.religion,
       p.mother_id,
       p.father_id,
       p.guardian_id,
       ep.nome AS school,
       string_agg(m.data_matricula::date::TEXT, '|') AS registration_date,
       string_agg(COALESCE(m.data_cancel::date, mt.data_exclusao::date)::TEXT,  '|') AS registration_out,
       m.ano AS year,
       vs.cod_situacao AS status,
       string_agg(vs.texto_situacao::TEXT, '|') AS status_text,
       a.cod_aluno AS student_id,
       a.aluno_estado_id AS registration_code_id,
       string_agg(m.cod_matricula::TEXT, '|') AS registration_id,
       string_agg(m.ref_cod_curso::TEXT, '|') AS course_id,
       string_agg(m.ref_ref_cod_serie::TEXT, '|') AS grade_id,
       m.ref_ref_cod_escola AS school_id,
       string_agg(t.cod_turma::TEXT, '|') AS school_class_id,
       string_agg(t.tipo_atendimento::TEXT, '|') AS attendance_type,
       string_agg(ece.cod_escola_inep::TEXT, '|') AS school_inep,
       string_agg(t.etapa_educacenso::TEXT, '|') AS school_class_stage,
       string_agg(COALESCE(tm.nome, tt.nome)::TEXT, '|')  AS period,
       array_to_string(ARRAY(
            SELECT json_array_elements_text(ma.recursos_tecnologicos) AS json_array_elements_text
        ), ';'::text) AS technological_resources,
       p.nationality,
       p.birthplace,
       string_agg((CASE m.modalidade_ensino
                       WHEN 0 THEN 'Semipresencial'::varchar
                       WHEN 1 THEN 'EAD'::varchar
                       WHEN 2 THEN 'Off-line'::varchar
                       ELSE 'Presencial'::varchar
           END), '|') AS modalidade_ensino,
       me.cod_aluno_inep AS inep_id
FROM exporter_person p
         JOIN pmieducar.aluno a ON p.id = a.ref_idpes::numeric
         JOIN pmieducar.matricula m ON m.ref_cod_aluno = a.cod_aluno
         JOIN pmieducar.escola e ON e.cod_escola = m.ref_ref_cod_escola
         JOIN cadastro.pessoa ep ON ep.idpes = e.ref_idpes::numeric
         JOIN pmieducar.serie s ON s.cod_serie = m.ref_ref_cod_serie
         JOIN pmieducar.curso c ON c.cod_curso = m.ref_cod_curso
         JOIN pmieducar.matricula_turma mt ON mt.ref_cod_matricula = m.cod_matricula
         JOIN relatorio.view_situacao vs ON vs.cod_matricula = m.cod_matricula AND
                                            vs.cod_turma = mt.ref_cod_turma AND
                                            vs.sequencial = mt.sequencial
         JOIN pmieducar.turma t ON t.cod_turma = mt.ref_cod_turma
         LEFT JOIN modules.educacenso_cod_escola ece ON e.cod_escola = ece.cod_escola
         LEFT JOIN pmieducar.turma_turno tt ON tt.id = t.turma_turno_id
         LEFT JOIN pmieducar.turma_turno tm ON tm.id = mt.turno_id
         LEFT JOIN modules.moradia_aluno ma ON ma.ref_cod_aluno = a.cod_aluno
         LEFT JOIN modules.educacenso_cod_aluno me ON me.cod_aluno = a.cod_aluno
WHERE true AND a.ativo = 1 AND m.ativo = 1

GROUP BY p.id,
         p.name,
         p.social_name,
         p.cpf,
         p.rg,
         p.rg_issue_date,
         p.rg_state_abbreviation,
         p.date_of_birth,
         p.email,
         p.sus,
         p.nis,
         p.occupation,
         p.organization,
         p.monthly_income,
         p.gender,
         p.race,
         p.religion,
         p.mother_id,
         p.father_id,
         p.guardian_id,
         a.cod_aluno,
         a.aluno_estado_id,
         array_to_string(ARRAY( SELECT json_array_elements_text(ma.recursos_tecnologicos) AS json_array_elements_text), ';'::text),
         p.nationality,
         p.birthplace,
         ep.nome,
         m.ano,
         m.ref_ref_cod_escola,
         vs.cod_situacao,
         me.cod_aluno_inep
ORDER BY a.ref_idpes;
