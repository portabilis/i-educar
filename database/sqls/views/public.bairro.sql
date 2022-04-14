create or replace view public.bairro as
select
	city_id as idmun,
	null::varchar as geom,
	id as idbai,
	neighborhood as nome,
	null::integer as idpes_rev,
	null::timestamp as data_rev,
	'U'::bpchar as origem_gravacao,
	1 as idpes_cad,
	now() as data_cad,
	'I'::bpchar as operacao,
	1 as zona_localizacao,
	id as iddis,
	null::integer as idsetorbai
from places;
