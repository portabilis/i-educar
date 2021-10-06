create view public.exporter_benefits as
select
	aab.aluno_id as student_id,
	string_agg(ab.nm_beneficio, ', ') as benefits
from pmieducar.aluno_aluno_beneficio aab
inner join pmieducar.aluno_beneficio ab
on ab.cod_aluno_beneficio = aab.aluno_beneficio_id
group by aab.aluno_id
order by aab.aluno_id
