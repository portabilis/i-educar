INSERT INTO modules.componente_curricular (id, instituicao_id, area_conhecimento_id, nome, abreviatura, tipo_base, codigo_educacenso, ordenamento) VALUES
	(10001, 1, (SELECT id FROM modules.area_conhecimento WHERE nome = 'Infantil'), 'Traços, sons, cores e formas', 'Tscf', 1, 99, 1),
	(10002, 1, (SELECT id FROM modules.area_conhecimento WHERE nome = 'Infantil'), 'O eu, o outro e o nós', 'Eon', 1, 99, 2),
	(10003, 1, (SELECT id FROM modules.area_conhecimento WHERE nome = 'Infantil'), 'Corpo, gestos e movimentos', 'Cgm', 1, 99, 3),
	(10004, 1, (SELECT id FROM modules.area_conhecimento WHERE nome = 'Infantil'), 'Escuta, fala, pensamento e imaginação', 'Efpi', 1, 99, 4),
	(10005, 1, (SELECT id FROM modules.area_conhecimento WHERE nome = 'Infantil'), 'Espaços, tempos, quantidades, relações e transformações', 'Etqrt', 1, 99, 5);