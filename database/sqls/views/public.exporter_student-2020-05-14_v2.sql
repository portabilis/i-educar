create or replace view public.exporter_student as
select
    p.*,
    ep.nome as school,
    t.nm_turma as school_class,
    s.nm_serie as grade,
    c.nm_curso as course,
    m.data_matricula as registration_date,
    m.ano as year,
    vs.cod_situacao as status,
    vs.texto_situacao as status_text,
    a.cod_aluno as student_id,
    m.cod_matricula as registration_id,
    m.ref_cod_curso as course_id,
    m.ref_ref_cod_serie as grade_id,
    m.ref_ref_cod_escola as school_id,
    t.cod_turma as school_class_id,
    t.tipo_atendimento as attendance_type,
    ece.cod_escola_inep as school_inep,
    t.etapa_educacenso as school_class_stage,
    coalesce(tm.nome, tt.nome) as period,
    array_to_string(ARRAY(SELECT json_array_elements_text(recursos_tecnologicos)), ';') as technological_resources
from public.exporter_person p
         inner join pmieducar.aluno a
                    on p.id = a.ref_idpes
         inner join pmieducar.matricula m
                    on m.ref_cod_aluno = a.cod_aluno
         inner join pmieducar.escola e
                    on e.cod_escola = m.ref_ref_cod_escola
         inner join cadastro.pessoa ep
                    on ep.idpes = e.ref_idpes
         inner join pmieducar.serie s
                    on s.cod_serie = m.ref_ref_cod_serie
         inner join pmieducar.curso c
                    on c.cod_curso = m.ref_cod_curso
         inner join pmieducar.matricula_turma mt
                    on mt.ref_cod_matricula = m.cod_matricula
         inner join relatorio.view_situacao vs
                    on vs.cod_matricula = m.cod_matricula
                        and vs.cod_turma = mt.ref_cod_turma
                        and vs.sequencial = mt.sequencial
         inner join pmieducar.turma t
                    on t.cod_turma = mt.ref_cod_turma
         left join modules.educacenso_cod_escola ece
                   on e.cod_escola = ece.cod_escola
         left join pmieducar.turma_turno tt
                   on tt.id = t.turma_turno_id
         left join pmieducar.turma_turno tm
                   on tm.id = mt.turno_id
         left join modules.moradia_aluno ma
		on ma.ref_cod_aluno = a.cod_aluno
where true
  and a.ativo = 1
  and m.ativo = 1
order by a.ref_idpes
