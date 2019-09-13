CREATE OR REPLACE VIEW individuals AS
SELECT
    idpes AS id,
    idpes AS person_id,
    nome_social AS social_name,
    data_nasc AS birthdate,
    (CASE sexo
        WHEN 'M' THEN 1 -- masculino
        WHEN 'F' THEN 2 -- feminino
        ELSE NULL
    END) AS gender,
    idpes_mae AS mother_individual_id,
    idpes_pai AS father_individual_id,
    idpes_responsavel AS guardian_individual_id,
    -- idesco,
    -- ideciv,
    -- idpes_con,
    -- data_uniao,
    -- data_obito,
    nacionalidade AS nationality,
    idpais_estrangeiro AS country_id,
    data_chegada_brasil AS arrived_at,
    idmun_nascimento AS city_id,
    -- ultima_empresa,
    -- idocup numeric(6,0),
    nome_mae AS mother_name,
    nome_pai AS father_name,
    nome_conjuge AS spouse_name,
    nome_responsavel AS guardian_name,
    -- justificativa_provisorio,
    idpes_rev AS updated_by,
    data_rev::timestamp(0) AS updated_at,
    (CASE origem_gravacao
        WHEN 'M' THEN 1 -- migração
        WHEN 'C' THEN 2 -- cadastro
        WHEN 'U' THEN 3 -- unificação
        WHEN 'O' THEN 4 -- outro
    END) AS registry_origin,
    idpes_cad AS created_by,
    data_cad::timestamp(0) AS created_at,
    -- operacao,
    -- ref_cod_sistema,
    cpf,
    ref_cod_religiao AS religion_id,
    nis_pis_pasep,
    sus,
    ocupacao AS ocupation,
    empresa AS company,
    pessoa_contato AS contact_name,
    renda_mensal AS monthly_income,
    data_admissao AS admitted_at,
    ddd_telefone_empresa AS company_area_code,
    telefone_empresa AS company_phone,
    falecido AS deceased,
    (CASE
        WHEN ativo = 0 THEN data_exclusao
        ELSE NULL
    END)::timestamp(0) AS deleted_at,
    ref_usuario_exc AS deleted_by,
    zona_localizacao_censo AS localization_zone,
    tipo_trabalho AS job_type,
    local_trabalho AS job_location,
    horario_inicial_trabalho AS job_start_time,
    horario_final_trabalho AS job_end_time
FROM cadastro.fisica;
