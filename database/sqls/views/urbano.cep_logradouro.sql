create or replace view urbano.cep_logradouro as
select
	id as idlog,
	postal_code as cep,
	null::integer as nroini,
	null::integer as nrofin,
	null::integer as idpes_rev,
	null::timestamp as data_rev,
	'U'::bpchar as origem_gravacao,
	1 as idpes_cad,
	now() as data_cad,
	'I'::bpchar as operacao
from places
group by idlog, cep;
