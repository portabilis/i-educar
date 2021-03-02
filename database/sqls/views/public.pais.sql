create or replace view public.pais as
select
	id as idpais,
	"name" as nome,
	null::varchar as geom,
	ibge_code as cod_ibge
from countries;
