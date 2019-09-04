create or replace view persons_has_places as
select
	idpes as "person_id",
	idlog as "place_id",
	tipo as "type"
from cadastro.endereco_pessoa;
