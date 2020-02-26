create or replace view public.municipio as
select
	c.id as idmun,
	c."name" as nome,
	s.abbreviation as sigla_uf,
	null::integer as area_km2,
	null::integer as idmreg,
	null::integer as idasmun,
	c.ibge_code as cod_ibge,
	null::varchar as geom,
	'M'::bpchar as tipo,
	null::integer as idmun_pai,
	null::integer as idpes_rev,
	1 as idpes_cad,
	null::timestamp as data_rev,
	now() as data_cad,
	'M'::bpchar as origem_gravacao,
	'I'::bpchar as operacao
from cities c
inner join states s
on s.id = c.state_id;
