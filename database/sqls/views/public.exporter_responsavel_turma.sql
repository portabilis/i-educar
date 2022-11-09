create view public.exporter_responsaveis_turma as
SELECT p.id,
    p.name as nome_aluno,
    p.guardian_id,
    p.mother_id,
    p.father_id,
    fs.idpes_responsavel AS id_pes_responsavel,
    ep.nome AS school,
    t.nm_turma AS school_class,
    s.nm_serie AS grade,
    c.nm_curso AS course,
    m.data_matricula::date AS registration_date,
    COALESCE(m.data_cancel::date, mt.data_exclusao::date) AS registration_out,
    m.ano AS year,
    vs.cod_situacao AS status,
    vs.texto_situacao AS status_text,
    a.cod_aluno AS student_id,
    a.tipo_responsavel AS tipo_responsavel,
    m.cod_matricula AS registration_id,
    m.ref_cod_curso AS course_id,
    m.ref_ref_cod_serie AS grade_id,
    m.ref_ref_cod_escola AS school_id,
    m.ref_ref_cod_serie AS level_id,
     CASE
        WHEN m.ativo = 0 THEN m.data_exclusao
        ELSE NULL::timestamp without time zone
    END::timestamp(0) without time zone AS deleted_at,
    m.cod_matricula as id_matricula,
    t.cod_turma AS school_class_id,
    t.tipo_atendimento AS attendance_type,
    ece.cod_escola_inep AS school_inep,
    t.etapa_educacenso AS school_class_stage,
    COALESCE(tm.nome, tt.nome) AS period,
    array_to_string(ARRAY( SELECT json_array_elements_text(ma.recursos_tecnologicos) AS json_array_elements_text), ';'::text) AS technological_resources,
        CASE m.modalidade_ensino
            WHEN 0 THEN 'Semipresencial'::character varying
            WHEN 1 THEN 'EAD'::character varying
            WHEN 2 THEN 'Off-line'::character varying
            ELSE 'Presencial'::character varying
        END AS modalidade_ensino,

          pr.idpes AS id_resp,
          pr.nome AS name,
          f.nome_social AS social_name,
          f.cpf AS cpf,
          d.rg AS rg,
          pl.address AS endereco,
          pl.number AS numero_casa,
          pl.neighborhood AS bairro,
          ci.name AS naturalidade,
          pl.postal_code AS cep,
          pl.complement AS complemento,
          fon.ddd AS ddd,
          fon.fone AS telefone,
          es.descricao AS estado_civil,
          d.data_exp_rg AS rg_issue_date,
          d.sigla_uf_exp_rg AS rg_state_abbreviation,
          d.zona_tit_eleitor AS zona,
          d.secao_tit_eleitor AS secao,
          d.num_tit_eleitor AS titulo,
          d.certidao_nascimento AS certidao,
          d.cartorio_cert_civil AS cartorio_emissao,
          d.sigla_uf_cert_civil AS estado_emissao_cn,
          d.data_emissao_cert_civil AS data_exp_certidao,
          pfs.nm_profissao AS profession,
          f.data_nasc AS date_of_birth,
          pr.email AS email_address,
          f.sus AS sus_number,
          f.nis_pis_pasep AS nis,
          f.empresa AS organization,
          f.agencia AS bank_branch,
          f.conta AS bank_account,
          f.tipo_conta AS type_bank_account,
          f.renda_mensal AS monthly_income,
          f.sexo AS gender,
          f.idpes_mae AS mother_id_resp,
          f.idpes_pai AS father_id_resp,
          f.idpes_responsavel AS guardian_id_responsavel,
           case f.nacionalidade
              when 1 then 'Brasileira'::varchar
              when 2 then 'Naturalizado brasileiro'::varchar
              when 3 then 'Estrangeira'::varchar
              else 'Não informado'::varchar
          end as nationality,
    COALESCE( ci."name"||' - '||st.abbreviation , 'Não informado') as birthplace,

    prpai.idpes AS id_pai,
          prpai.nome AS name_pai,
          fpai.nome_social AS social_name_pai,
          fpai.cpf AS cpf_pai,
          dpai.rg AS rg_pai,
          plpai.address AS endereco_pai,
          plpai.number AS numero_casa_pai,
          plpai.neighborhood AS bairro_pai,
          cipai.name AS naturalidade_pai,
          plpai.postal_code AS cep_pai,
          plpai.complement AS complemento_pai,
          fonpai.ddd AS ddd_pai,
          fonpai.fone AS telefone_pai,
          espai.descricao AS estado_civil_pai,
          dpai.data_exp_rg AS rg_issue_date_pai,
          dpai.sigla_uf_exp_rg AS rg_state_abbreviation_pai,
          dpai.zona_tit_eleitor AS zona_pai,
          dpai.secao_tit_eleitor AS secao_pai,
          dpai.num_tit_eleitor AS titulo_pai,
          dpai.certidao_nascimento AS certidao_pai,
          dpai.cartorio_cert_civil AS cartorio_emissao_pai,
          dpai.sigla_uf_cert_civil AS estado_emissao_cn_pai,
          dpai.data_emissao_cert_civil AS data_exp_certidao_pai,
          pfspai.nm_profissao AS profession_pai,
          fpai.data_nasc AS date_of_birth_pai,
          prpai.email AS email_address_pai,
          fpai.sus AS sus_number_pai,
          fpai.nis_pis_pasep AS nis_pai,
          fpai.empresa AS organization_pai,
          fpai.agencia AS bank_branch_pai,
          fpai.conta AS bank_account_pai,
          fpai.tipo_conta AS type_bank_account_pai,
          fpai.renda_mensal AS monthly_income_pai,
          fpai.sexo AS gender_pai,
          fpai.idpes_mae AS mother_id_resp_pai,
          fpai.idpes_pai AS father_id_resp_pai,
          fpai.idpes_responsavel AS guardian_id_responsavel_pai,
          
           case fpai.nacionalidade
              when 1 then 'Brasileira'::varchar
              when 2 then 'Naturalizado brasileiro'::varchar
              when 3 then 'Estrangeira'::varchar
              else 'Não informado'::varchar
          end as nationality_pai,
    COALESCE( ci."name"||' - '||st.abbreviation , 'Não informado') as birthplace_pai,

    prmae.idpes AS id_mae,
          prmae.nome AS name_mae,
          fmae.nome_social AS social_name_mae,
          fmae.cpf AS cpf_mae,
          dmae.rg AS rg_mae,
          plmae.address AS endereco_mae,
          plmae.number AS numero_casa_mae,
          plmae.neighborhood AS bairro_mae,
          cimae.name AS naturalidade_mae,
          plmae.postal_code AS cep_mae,
          plmae.complement AS complemento_mae,
          fonmae.ddd AS ddd_mae,
          fonmae.fone AS telefone_mae,
          esmae.descricao AS estado_civil_mae,
          dmae.data_exp_rg AS rg_issue_date_mae,
          dmae.sigla_uf_exp_rg AS rg_state_abbreviation_mae,
          dmae.zona_tit_eleitor AS zona_mae,
          dmae.secao_tit_eleitor AS secao_mae,
          dmae.num_tit_eleitor AS titulo_mae,
          dmae.certidao_nascimento AS certidao_mae,
          dmae.cartorio_cert_civil AS cartorio_emissao_mae,
          dmae.sigla_uf_cert_civil AS estado_emissao_cn_mae,
          dmae.data_emissao_cert_civil AS data_exp_certidao_mae,
          pfsmae.nm_profissao AS profession_mae,
          fmae.data_nasc AS date_of_birth_mae,
          prmae.email AS email_address_mae,
          fmae.sus AS sus_number_mae,
          fmae.nis_pis_pasep AS nis_mae,
          fmae.empresa AS organization_mae,
          fmae.agencia AS bank_branch_mae,
          fmae.conta AS bank_account_mae,
          fmae.tipo_conta AS type_bank_account_mae,
          fmae.renda_mensal AS monthly_income_mae,
          fmae.sexo AS gender_mae,
          fmae.idpes_mae AS mother_id_resp_mae,
          fmae.idpes_pai AS father_id_resp_mae,
          fmae.idpes_responsavel AS guardian_id_responsavel_mae,
           case fmae.nacionalidade
              when 1 then 'Brasileira'::varchar
              when 2 then 'Naturalizado brasileiro'::varchar
              when 3 then 'Estrangeira'::varchar
              else 'Não informado'::varchar
          end as nationality_mae,
    COALESCE( ci."name"||' - '||st.abbreviation , 'Não informado') as birthplace_mae

   FROM exporter_person p
     JOIN fisica fs ON p.id = fs.idpes
     JOIN aluno a ON p.id = a.ref_idpes::numeric
     LEFT JOIN matricula m ON m.ref_cod_aluno = a.cod_aluno
     JOIN escola e ON e.cod_escola = m.ref_ref_cod_escola
     LEFT JOIN pessoa ep ON ep.idpes = e.ref_idpes::numeric
     JOIN serie s ON s.cod_serie = m.ref_ref_cod_serie
     JOIN curso c ON c.cod_curso = m.ref_cod_curso
     LEFT JOIN matricula_turma mt ON mt.ref_cod_matricula = m.cod_matricula
     JOIN relatorio.view_situacao vs ON vs.cod_matricula = m.cod_matricula AND vs.cod_turma = mt.ref_cod_turma AND vs.sequencial = mt.sequencial
     JOIN turma t ON t.cod_turma = mt.ref_cod_turma
     LEFT JOIN educacenso_cod_escola ece ON e.cod_escola = ece.cod_escola
     LEFT JOIN turma_turno tt ON tt.id = t.turma_turno_id
     LEFT JOIN turma_turno tm ON tm.id = mt.turno_id
     LEFT JOIN moradia_aluno ma ON ma.ref_cod_aluno = a.cod_aluno

     LEFT JOIN cadastro.fisica f ON f.idpes = guardian_id
     LEFT JOIN cadastro.pessoa pr ON pr.idpes = guardian_id
     LEFT JOIN cadastro.documento d ON d.idpes = guardian_id
     LEFT JOIN cadastro.estado_civil es ON es.ideciv = f.ideciv
     LEFT JOIN cadastro.fone_pessoa fon ON fon.idpes = guardian_id
     LEFT JOIN cadastro.profissao pfs ON pfs.cod_profissao::varchar = f.ref_cod_profissao::varchar
     LEFT JOIN public.person_has_place plc ON plc.person_id = guardian_id
     LEFT JOIN public.places pl ON pl.id = plc.place_id
     LEFT JOIN public.cities ci ON ci.id = f.idmun_nascimento
     LEFT JOIN public.states st on ci.state_id = st.id

     LEFT JOIN cadastro.fisica fpai ON fpai.idpes = father_id
     LEFT JOIN cadastro.pessoa prpai ON prpai.idpes = father_id
     LEFT JOIN cadastro.documento dpai ON dpai.idpes = father_id
     LEFT JOIN cadastro.estado_civil espai ON espai.ideciv = fpai.ideciv
     LEFT JOIN cadastro.fone_pessoa fonpai ON fonpai.idpes = father_id
     LEFT JOIN cadastro.profissao pfspai ON pfspai.cod_profissao::varchar = fpai.ref_cod_profissao::varchar
     LEFT JOIN public.person_has_place plcpai ON plcpai.person_id = father_id
     LEFT JOIN public.places plpai ON plpai.id = plcpai.place_id
     LEFT JOIN public.cities cipai ON cipai.id = fpai.idmun_nascimento
     LEFT JOIN public.states stpai ON cipai.state_id = stpai.id

     LEFT JOIN cadastro.fisica fmae ON fmae.idpes = mother_id
     LEFT JOIN cadastro.pessoa prmae ON prmae.idpes = mother_id
     LEFT JOIN cadastro.documento dmae ON dmae.idpes = mother_id
     LEFT JOIN cadastro.estado_civil esmae ON esmae.ideciv = fmae.ideciv
     LEFT JOIN cadastro.fone_pessoa fonmae ON fonmae.idpes = mother_id
     LEFT JOIN cadastro.profissao pfsmae ON pfsmae.cod_profissao::varchar = fmae.ref_cod_profissao::varchar
     LEFT JOIN public.person_has_place plcmae ON plcmae.person_id = mother_id
     LEFT JOIN public.places plmae ON plmae.id = plcmae.place_id
     LEFT JOIN public.cities cimae ON cimae.id = fmae.idmun_nascimento
     LEFT JOIN public.states stmae ON cimae.state_id = stmae.id
    
  WHERE true AND a.ativo = 1 AND m.ativo = 1;
