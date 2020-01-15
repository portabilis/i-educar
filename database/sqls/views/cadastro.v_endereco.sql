create or replace view cadastro.v_endereco as
select
	p.person_id as idpes,
	a.postal_code as cep,
	a.id as idlog,
	a.number as numero,
	null::varchar as letra,
	a.complement as complemento,
	a.id as idbai,
	null::varchar as bloco,
	null::integer as andar,
	null::integer as apartamento,
	a.address as logradouro,
	a.id as idtlog,
	a.neighborhood as bairro,
	a.city as cidade,
	a.state_abbreviation as sigla_uf,
	1 as zona_localizacao
from person_has_place p
inner join addresses a
on a.id = p.place_id;
