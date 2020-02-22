create or replace view addresses as
select
	p.id,
	p.city_id,
	c.state_id,
	s.country_id,
	p.address,
	p."number",
	p.complement,
	p.neighborhood,
	p.postal_code,
	p.latitude,
	p.longitude,
	c."name" as city,
	s.abbreviation as state_abbreviation,
	s."name" as state,
	cn."name" as country,
	c.ibge_code as city_ibge_code,
	s.ibge_code as state_ibge_code,
	cn.ibge_code as country_ibge_code
from places p
inner join cities c
on c.id = p.city_id
inner join states s
on s.id = c.state_id
inner join countries cn
on cn.id = s.country_id;
