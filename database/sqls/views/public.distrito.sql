create or replace view public.distrito as
select
    c.id as idmun,
    null::varchar as geom,
    p.id as iddis,
    c.name as nome,
    c.ibge_code as cod_ibge,
    null::integer as idpes_rev,
    null::timestamp as data_rev,
    'M'::bpchar as origem_gravacao,
    1 as idpes_cad,
    now() as data_cad,
    'I'::bpchar as operacao
from places p
inner join cities c
on c.id = p.city_id;
