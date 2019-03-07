CREATE OR REPLACE VIEW registrations AS
SELECT
    cod_matricula AS id,
    ref_cod_aluno AS student_id,
    ref_ref_cod_escola AS school_id,
    ref_cod_curso AS course_id,
    ref_ref_cod_serie AS level_id,
    ref_cod_abandono_tipo AS abandonment_id,
    ref_cod_reserva_vaga AS reservation_id,
    turno_pre_matricula AS reservation_shift_id,
    aprovado AS status,
    ano AS year,
    semestre AS semester,
    observacao AS observations,
    descricao_reclassificacao AS reclassification_description,
    -- modulo,
    ultima_matricula AS is_last_registration,
    formando AS is_graduand,
    matricula_reclassificacao AS is_reclassified,
    matricula_transferencia AS is_transference,
    dependencia AS is_dependency,
    saida_escola AS has_left_school,
    ref_usuario_cad AS created_by,
    ref_usuario_exc AS deleted_by,
    data_matricula AS registrated_at,
    data_cancel AS canceled_at,
    data_saida_escola AS left_school_at,
    data_cadastro::timestamp(0) AS created_at,
    updated_at::timestamp(0) AS updated_at,
    (CASE
        WHEN ativo = 0 THEN data_exclusao
        ELSE NULL
    END)::timestamp(0) AS deleted_at
FROM pmieducar.matricula;
