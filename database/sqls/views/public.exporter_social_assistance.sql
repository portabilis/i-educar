create or replace view public.exporter_social_assistance as
select
    p.name,
    p.cpf,
    p.rg,
    p.date_of_birth,
    p.nis,
    ep.nome AS school,
    ece.cod_escola_inep AS school_inep,
    t.etapa_educacenso AS school_class_stage,
    COALESCE(tm.nome, tt.nome) AS period,
    t.tipo_atendimento AS attendance_type,
    vs.cod_situacao AS status,
    m.ano AS year,
    m.ref_ref_cod_escola AS school_id
FROM exporter_person p
    JOIN pmieducar.aluno a ON p.id = a.ref_idpes::numeric
    JOIN pmieducar.matricula m ON m.ref_cod_aluno = a.cod_aluno
    JOIN pmieducar.escola e ON e.cod_escola = m.ref_ref_cod_escola
    JOIN cadastro.pessoa ep ON ep.idpes = e.ref_idpes::numeric
    JOIN pmieducar.matricula_turma mt ON mt.ref_cod_matricula = m.cod_matricula
    JOIN relatorio.view_situacao vs ON vs.cod_matricula = m.cod_matricula AND vs.cod_turma = mt.ref_cod_turma AND vs.sequencial = mt.sequencial
    JOIN pmieducar.turma t ON t.cod_turma = mt.ref_cod_turma
    LEFT JOIN modules.educacenso_cod_escola ece ON e.cod_escola = ece.cod_escola
    LEFT JOIN pmieducar.turma_turno tt ON tt.id = t.turma_turno_id
    LEFT JOIN pmieducar.turma_turno tm ON tm.id = mt.turno_id
WHERE true AND a.ativo = 1 AND m.ativo = 1
ORDER BY a.ref_idpes;
