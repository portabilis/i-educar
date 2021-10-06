create view public.exporter_student as
select
	p.*,
	ep.nome as school,
	t.nm_turma as school_class,
	s.nm_serie as grade,
	c.nm_curso as course,
	m.data_matricula as registration_date,
	m.aprovado as status,
	m.ano as year,
	a.cod_aluno as student_id,
	m.cod_matricula as registration_id,
	m.ref_cod_curso as course_id,
	m.ref_ref_cod_serie as grade_id,
	m.ref_ref_cod_escola as school_id,
	t.cod_turma as school_class_id
from public.exporter_person p
inner join pmieducar.aluno a
on p.id = a.ref_idpes
inner join pmieducar.matricula m
on m.ref_cod_aluno = a.cod_aluno
inner join cadastro.pessoa ep
on ep.idpes = m.ref_ref_cod_escola
inner join pmieducar.serie s
on s.cod_serie = m.ref_ref_cod_serie
inner join pmieducar.curso c
on c.cod_curso = m.ref_cod_curso
inner join pmieducar.matricula_turma mt
on mt.ref_cod_matricula = m.cod_matricula
inner join pmieducar.turma t
on t.cod_turma = mt.ref_cod_turma
where true
and a.ativo = 1
and m.ativo = 1
and mt.ativo = 1
order by a.ref_idpes
