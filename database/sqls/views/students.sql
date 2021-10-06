CREATE OR REPLACE VIEW public.students AS
SELECT
    a.cod_aluno AS id,
    a.ref_cod_religiao AS religion_id,
    a.ref_usuario_exc AS deleted_by,
    a.ref_usuario_cad AS created_by,
    a.ref_idpes AS individual_id,
    a.data_cadastro::timestamp(0) AS created_at,
    (CASE
        WHEN a.ativo = 0 THEN a.data_exclusao
        ELSE NULL
    END)::timestamp(0) AS deleted_at,
    a.caminho_foto AS picture_path,
    a.analfabeto AS illiterate,
    a.nm_pai AS father_name,
    a.nm_mae AS mother_name,
    (CASE a.tipo_responsavel
        WHEN 'p' THEN 1 -- pai
        WHEN 'm' THEN 2 -- mãe
        WHEN 'a' THEN 3 -- pai e mãe
        WHEN 'r' THEN 4 -- outra pessoa
        ELSE NULL
    END) AS guardian_type,
    a.aluno_estado_id AS registry_code, -- RA
    a.justificativa_falta_documentacao AS missing_docs_rationale,
    a.url_laudo_medico AS medical_report_path,
    a.codigo_sistema AS system_code,
    ta.responsavel AS transportation_provider,
    a.veiculo_transporte_escolar AS transportation_vehicle_type,
    a.autorizado_um AS pickup_authorized_first,
    a.parentesco_um AS pickup_kinship_first,
    a.autorizado_dois AS pickup_authorized_second,
    a.parentesco_dois AS pickup_kinship_second,
    a.autorizado_tres AS pickup_authorized_third,
    a.parentesco_tres AS pickup_kinship_third,
    a.autorizado_quatro AS pickup_authorized_fourth,
    a.parentesco_quatro AS pickup_kinship_fourth,
    a.autorizado_cinco AS pickup_authorized_fifth,
    a.parentesco_cinco AS pickup_kinship_fifth,
    a.url_documento AS document_path,
    a.recebe_escolarizacao_em_outro_espaco AS schooling_in_other_space,
    a.recursos_prova_inep AS inep_test_resources
FROM pmieducar.aluno a
LEFT JOIN modules.transporte_aluno ta ON ta.aluno_id = a.cod_aluno;
