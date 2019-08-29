create or replace view places as
select
	ep.idpes || '-' || ep.idlog as "id",
	ep.idpes as "person_id",
	ep.idlog as "place_id",
	ep.tipo as "type_id",
	tl.idtlog as "address_type_id",
	ep.idbai as "neighborhood_id",
	b.idmun as "city_id",
	concat(tl.descricao, ' ', l.nome) as "address",
	ep.numero as "number",
	ep.complemento as "complement",
	ep.cep as "postal_code",
	b.nome as "neighborhood",
    ep.idpes_cad AS created_by,
    ep.idpes_rev AS updated_by,
    ep.data_cad::timestamp(0) AS created_at,
    ep.data_rev::timestamp(0) AS updated_at
from cadastro.endereco_pessoa ep
inner join public.logradouro l
on l.idlog = ep.idlog
inner join urbano.tipo_logradouro tl
on tl.idtlog = l.idtlog
inner join urbano.cep_logradouro cl
on cl.idlog = l.idlog
and cl.cep = ep.cep
inner join public.bairro b
on b.idbai = ep.idbai;
