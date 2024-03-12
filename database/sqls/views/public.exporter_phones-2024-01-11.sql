create or replace view public.exporter_phones as
select
	p.idpes as person_id,
	string_agg(concat('(', fp.ddd, ') ', fp.fone), ', ') as phones
from cadastro.pessoa p
inner join cadastro.fone_pessoa fp
on fp.idpes = p.idpes
where NULLIF(fp.fone, 0) is not null and NULLIF(fp.ddd, 0) is not null
group by p.idpes
order by p.idpes
