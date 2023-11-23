create view public.exporter_projects as
select
	pa.ref_cod_aluno as student_id,
	string_agg(p.nome, ', ') as projects
from pmieducar.projeto_aluno pa
inner join pmieducar.projeto p
on p.cod_projeto = pa.ref_cod_projeto
where pa.data_desligamento is null
group by pa.ref_cod_aluno
order by pa.ref_cod_aluno
