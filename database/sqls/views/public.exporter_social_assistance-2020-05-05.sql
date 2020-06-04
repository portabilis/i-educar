create or replace view public.exporter_social_assistance as
select
    s.name,
    s.cpf,
    s.rg,
    s.date_of_birth,
    s.nis,
    s.school,
    s.school_inep,
    s.school_class_stage,
    s.period,
    s.attendance_type,
    s.status,
    s.year,
    s.school_id,
    array_to_string(array_agg(DISTINCT ec.nome), ';') as course_stage
from public.exporter_student s
left join modules.etapas_curso_educacenso ece
    on ece.curso_id = s.course_id
left join modules.etapas_educacenso ec
    on ec.id = ece.etapa_id
where true
group by 1,2,3,4,5,6,7,8,9,10,11,12,13
