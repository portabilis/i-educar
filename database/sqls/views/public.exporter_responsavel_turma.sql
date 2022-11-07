create view public.exporter_responsaveis_turma as
SELECT p.id,
    p.name as nome_aluno,
    p.guardian_id as guardian_id,
    p.mother_id,
    p.father_id,
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
    m.cod_matricula AS registration_id,
    m.ref_cod_curso AS course_id,
    m.ref_ref_cod_serie AS grade_id,
    m.ref_ref_cod_escola AS school_id,
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
    COALESCE( ci."name"||' - '||st.abbreviation , 'Não informado') as birthplace

   FROM exporter_person p

     JOIN aluno a ON p.id = a.ref_idpes::numeric
     JOIN matricula m ON m.ref_cod_aluno = a.cod_aluno
     JOIN escola e ON e.cod_escola = m.ref_ref_cod_escola
     JOIN pessoa ep ON ep.idpes = e.ref_idpes::numeric
     JOIN serie s ON s.cod_serie = m.ref_ref_cod_serie
     JOIN curso c ON c.cod_curso = m.ref_cod_curso
     JOIN matricula_turma mt ON mt.ref_cod_matricula = m.cod_matricula

     JOIN cadastro.fisica f ON f.idpes = guardian_id
     JOIN cadastro.pessoa pr ON pr.idpes = guardian_id
     


     JOIN relatorio.view_situacao vs ON vs.cod_matricula = m.cod_matricula AND vs.cod_turma = mt.ref_cod_turma AND vs.sequencial = mt.sequencial
     LEFT JOIN turma t ON t.cod_turma = mt.ref_cod_turma
     LEFT JOIN educacenso_cod_escola ece ON e.cod_escola = ece.cod_escola
     LEFT JOIN turma_turno tt ON tt.id = t.turma_turno_id
     LEFT JOIN turma_turno tm ON tm.id = mt.turno_id
     LEFT JOIN moradia_aluno ma ON ma.ref_cod_aluno = a.cod_aluno

     LEFT JOIN cadastro.documento d ON d.idpes = guardian_id
     LEFT JOIN cadastro.estado_civil es ON es.ideciv = f.ideciv
     LEFT JOIN cadastro.fone_pessoa fon ON fon.idpes = guardian_id
     LEFT JOIN cadastro.profissao pfs ON pfs.cod_profissao::varchar = f.ref_cod_profissao::varchar
     LEFT JOIN public.person_has_place plc ON plc.person_id = guardian_id
     LEFT JOIN public.places pl ON pl.id = plc.place_id
     LEFT JOIN public.cities ci ON ci.id = f.idmun_nascimento
     LEFT JOIN public.states st on ci.state_id = st.id

  WHERE true AND a.ativo = 1 AND m.ativo = 1
  ORDER BY a.ref_idpes;
