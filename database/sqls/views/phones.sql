create or replace view public.phones as
select
	idpes || '-' || tipo as "id",
	idpes as "person_id",
	tipo as "type_id",
	ddd as "area_code",
	fone as "number",
    idpes_cad AS "created_by",
    idpes_rev AS "updated_by",
    data_cad::timestamp(0) AS "created_at",
    data_rev::timestamp(0) AS "updated_at"
from cadastro.fone_pessoa;
