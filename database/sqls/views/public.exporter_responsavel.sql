create view public.exporter_responsavel
AS SELECT p.idpes AS id,
          p.nome AS name,
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
          p.email AS email_address,
          f.sus AS sus_number,
          f.nis_pis_pasep AS nis,
          f.empresa AS organization,
          f.agencia AS bank_branch,
          f.conta AS bank_account,
          f.tipo_conta AS type_bank_account,
          f.renda_mensal AS monthly_income,
          f.sexo AS gender,
          f.idpes_mae AS mother_id,
          f.idpes_pai AS father_id,
          f.idpes_responsavel AS guardian_id,
          case f.nacionalidade
              when 1 then 'Brasileira'::varchar
              when 2 then 'Naturalizado brasileiro'::varchar
              when 3 then 'Estrangeira'::varchar
              else 'Não informado'::varchar
          end as nationality,
    COALESCE( ci."name"||' - '||st.abbreviation , 'Não informado') as birthplace
   FROM cadastro.pessoa p
     JOIN cadastro.fisica f ON f.idpes = p.idpes
     LEFT JOIN cadastro.documento d ON d.idpes = p.idpes
     LEFT JOIN cadastro.estado_civil es ON es.ideciv = f.ideciv
     LEFT JOIN cadastro.fone_pessoa fon ON fon.idpes = p.idpes
     LEFT JOIN cadastro.profissao pfs ON pfs.cod_profissao::varchar = f.ref_cod_profissao::varchar
     LEFT JOIN public.person_has_place plc ON plc.person_id = p.idpes
     LEFT JOIN public.places pl ON pl.id = plc.place_id
     LEFT JOIN public.cities ci ON ci.id = f.idmun_nascimento
     LEFT JOIN public.states st on ci.state_id = st.id
  WHERE true AND f.ativo = 1;