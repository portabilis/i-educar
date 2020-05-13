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
    s.school_id
from public.exporter_student s
where true
