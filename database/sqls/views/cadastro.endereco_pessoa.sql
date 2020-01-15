create or replace view cadastro.endereco_pessoa as
select
    php.person_id as idpes,
    php.type as tipo,
    a.postal_code as cep,
    a.id as idlog,
    a.number as numero,
    null::varchar as letra,
    a.complement as complemento,
    null::date as reside_desde,
    a.id as idbai,
    null as idpes_rev,
    null as data_rev,
    'M' as origem_gravacao,
    1 as idpes_cad,
    now() as data_cad,
    'I' as operacao,
    null::varchar as bloco,
    null::integer as andar,
    null::integer as apartamento,
    null::varchar as observacoes
from person_has_place php
inner join addresses a
on a.id = php.place_id;
