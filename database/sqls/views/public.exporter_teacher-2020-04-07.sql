create view public.exporter_teacher as
select
	p.*,
	pt.ano as year,
	c.nm_curso as course,
	s.nm_serie as grade,
	ep.nome as school,
	t.nm_turma as school_class,
	c.cod_curso as course_id,
	s.cod_serie as grade_id,
	e.cod_escola as school_id,
	t.cod_turma as school_class_id,
	pt.id as pivot_id
from modules.professor_turma pt
inner join public.exporter_person p
on p.id = pt.servidor_id
inner join pmieducar.turma t
on t.cod_turma = pt.turma_id
inner join pmieducar.escola e
on e.cod_escola = t.ref_ref_cod_escola
inner join cadastro.pessoa ep
on ep.idpes = e.ref_idpes
inner join pmieducar.serie s
on s.cod_serie = t.ref_ref_cod_serie
inner join pmieducar.curso c
on c.cod_curso = t.ref_cod_curso
order by
	p.name,
	ep.nome,
	c.nm_curso,
	s.nm_serie,
	t.nm_turma;
