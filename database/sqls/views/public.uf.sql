create or replace view public.uf as
select
	s.abbreviation as sigla_uf,
	s."name" as nome,
	null::varchar as geom,
	s.country_id as idpais,
	s.ibge_code as cod_ibge
from states s;
