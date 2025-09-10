create view public.exporter_person as
select
    p.idpes as id,
    p.nome as name,
    f.nome_social as social_name,
    trim(to_char(f.cpf, '000"."000"."000"-"00')) AS cpf,
    d.rg as rg,
    d.data_exp_rg as rg_issue_date,
    d.sigla_uf_exp_rg as rg_state_abbreviation,
    f.data_nasc as date_of_birth,
    p.email as email,
    f.sus as sus,
    f.nis_pis_pasep as nis,
    f.ocupacao as occupation,
    f.empresa as organization,
    f.renda_mensal as monthly_income,
    f.sexo as gender,
    r.nm_raca as race,
    f.idpes_mae as mother_id,
    f.idpes_pai as father_id,
    f.idpes_responsavel as guardian_id,
    case f.nacionalidade
        when 1 then 'Brasileira'::varchar
        when 2 then 'Naturalizado brasileiro'::varchar
        when 3 then 'Estrangeira'::varchar
        else 'Não informado'::varchar
    end as nationality,
    COALESCE(ci."name" || '/' || st.abbreviation, 'Não informado') as birthplace,
    coe.name AS country_of_origin,
    re.name AS religion,
    case f.localizacao_diferenciada
        when 1 then 'Área de assentamento'
        when 2 then 'Terra indígena'
        when 3 then 'Comunidade quilombola'
        when 8 then 'Área onde se localizam povos e comunidades tradicionais'
        when 7 then 'Não está em área de localização diferenciada'
        else 'Não informado'
    end as localization_type
from cadastro.pessoa p
inner join cadastro.fisica f on f.idpes = p.idpes
left join cadastro.fisica_raca fr on fr.ref_idpes = f.idpes
left join cadastro.raca r on r.cod_raca = fr.ref_cod_raca
left join cadastro.documento d on d.idpes = p.idpes
left join public.cities ci on ci.id = f.idmun_nascimento
left join public.states st on ci.state_id = st.id
left join public.countries coe on coe.id = f.idpais_estrangeiro
left join pmieducar.religions re on re.id = f.ref_cod_religiao
where true
  and f.ativo = 1
