create view public.exporter_disabilities as
select
	fd.ref_idpes as person_id,
	string_agg(d.nm_deficiencia, ', ') as disabilities
from cadastro.deficiencia d
inner join cadastro.fisica_deficiencia fd
on fd.ref_cod_deficiencia = d.cod_deficiencia
group by fd.ref_idpes
order by fd.ref_idpes
