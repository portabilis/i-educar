create or replace view public.logradouro as
select
	id as idlog,
	id as idtlog,
	address as nome,
	city_id as idmun,
	null::varchar as geom,
	'N'::bpchar as ident_oficial,
	null::integer as idpes_rev,
	null::timestamp as data_rev,
	'U'::bpchar as origem_gravacao,
	1 as idpes_cad,
	now() as data_cad,
	'I'::bpchar as operacao
from places;
