create or replace view public.exporter_stages as
select *,
       (
               posted_scores OR
               posted_absences OR
               posted_descritive_opinions OR
               posted_general_absence OR
               posted_general_score OR
               posted_general_descritive_opinions
           ) AS posted_data
from public.exporter_school_stages
union
select *,
       (
               posted_scores OR
               posted_absences OR
               posted_descritive_opinions OR
               posted_general_absence OR
               posted_general_score OR
               posted_general_descritive_opinions
           ) AS posted_data
from public.exporter_school_class_stages
