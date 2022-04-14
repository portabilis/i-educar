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
	pt.id as pivot_id,
	servidor.cod_servidor,
	employee_graduation.complete as employee_graduation_complete,
	escolaridade.descricao as schooling_degree,
	employee_postgraduates.complete as employee_postgraduates_complete,
	CASE servidor.tipo_ensino_medio_cursado
		WHEN 1 THEN 'Formação Geral'
		WHEN 2 THEN 'Modalidade Normal (Magistério)'
		WHEN 3 THEN 'Curso Técnico'
		WHEN 4 THEN 'Magistério Indígena Modalidade Normal'
		ELSE ''
END AS high_school_type,
	form.continuing_education_course
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
left join pmieducar.servidor servidor
on pt.servidor_id = servidor.cod_servidor
left join cadastro.escolaridade
on escolaridade.idesco = servidor.ref_idesco,
	LATERAL (
		SELECT CONCAT_WS(', ',
				CASE WHEN (ARRAY[1] <@ scfc.curso_formacao_continuada)::bool THEN 'Creche (0 a 3 anos)'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[2] <@ scfc.curso_formacao_continuada)::bool THEN 'Pré-escola (4 e 5 anos)'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[3] <@ scfc.curso_formacao_continuada)::bool THEN 'Anos iniciais do ensino fundamental'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[4] <@ scfc.curso_formacao_continuada)::bool THEN 'Anos finais do ensino fundamental'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[5] <@ scfc.curso_formacao_continuada)::bool THEN 'Ensino médio'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[6] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação de jovens e adultos'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[7] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação especial'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[8] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação indígena'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[9] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação do campo'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[10] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação ambiental'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[11] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação em direitos humanos'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[12] <@ scfc.curso_formacao_continuada)::bool THEN 'Gênero e diversidade sexual'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[13] <@ scfc.curso_formacao_continuada)::bool THEN 'Direitos de criança e adolescente'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[14] <@ scfc.curso_formacao_continuada)::bool THEN 'Educação para as relações étnico-raciais e História e cultura Afro-Brasileira e Africana'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[17] <@ scfc.curso_formacao_continuada)::bool THEN 'Gestão Escolar'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[15] <@ scfc.curso_formacao_continuada)::bool THEN 'Outros'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[16] <@ scfc.curso_formacao_continuada)::bool THEN 'Nenhum'::VARCHAR ELSE NULL::VARCHAR END
				)
		 AS continuing_education_course
		FROM pmieducar.servidor as scfc
		WHERE servidor.curso_formacao_continuada IS NOT NULL
		AND curso_formacao_continuada != '{}'
		and scfc.cod_servidor = servidor.cod_servidor
    ) form,
    LATERAL (
         SELECT STRING_AGG(
			('['||educacenso_curso_superior.nome || ', '
			||completion_year
			||', '||educacenso_ies.nome
			||', '||employee_graduation_disciplines.name
			||']')::varchar, ';') as complete
		 FROM employee_graduations
		 JOIN modules.educacenso_curso_superior ON educacenso_curso_superior.id = employee_graduations.course_id
		 JOIN modules.educacenso_ies ON educacenso_ies.id = employee_graduations.college_id
		 JOIN employee_graduation_disciplines ON employee_graduations.discipline_id = employee_graduation_disciplines.id
		WHERE employee_graduations.employee_id = servidor.cod_servidor
    ) AS employee_graduation,
	LATERAL (
		SELECT CONCAT_WS(', ',
				CASE WHEN (ARRAY[1] <@ serv.pos_graduacao)::bool THEN 'Especialização'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[2] <@ serv.pos_graduacao)::bool THEN 'Mestrado'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[3] <@ serv.pos_graduacao)::bool THEN 'Doutorado'::VARCHAR ELSE NULL::VARCHAR END,
				CASE WHEN (ARRAY[4] <@ serv.pos_graduacao)::bool THEN 'Não tem pós-graduação concluída'::VARCHAR ELSE NULL::VARCHAR END
				)
		 AS complete
		FROM pmieducar.servidor as serv
		where serv.cod_servidor = servidor.cod_servidor
	) AS employee_postgraduates
order by
	p.name,
	ep.nome,
	c.nm_curso,
	s.nm_serie,
	t.nm_turma;
