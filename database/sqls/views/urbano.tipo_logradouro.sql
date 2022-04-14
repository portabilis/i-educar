create or replace view urbano.tipo_logradouro as
select
    id as idtlog,
    ''::varchar as descricao
from places;
