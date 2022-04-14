create view public.exporter_person as
select
	p.idpes as id,
	p.nome as name,
	f.nome_social as social_name,
	f.cpf as cpf,
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
	f.idpes_mae as mother_id,
	f.idpes_pai as father_id,
	f.idpes_responsavel as guardian_id
from cadastro.pessoa p
inner join cadastro.fisica f
on f.idpes = p.idpes
left join cadastro.documento d
on d.idpes = p.idpes
where true
and f.ativo = 1
