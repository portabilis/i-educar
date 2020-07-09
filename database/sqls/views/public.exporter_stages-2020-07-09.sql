create or replace view public.exporter_stages as
select * from public.exporter_school_stages
union
select * from public.exporter_school_class_stages
