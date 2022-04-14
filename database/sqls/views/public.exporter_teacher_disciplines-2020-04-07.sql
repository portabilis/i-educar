create view public.exporter_teacher_disciplines as
select
	ptd.professor_turma_id as pivot_id,
	string_agg(cc.nome, ', ') as disciplines
from modules.professor_turma_disciplina ptd
inner join modules.componente_curricular cc
on cc.id = ptd.componente_curricular_id
group by ptd.professor_turma_id
order by ptd.professor_turma_id;
