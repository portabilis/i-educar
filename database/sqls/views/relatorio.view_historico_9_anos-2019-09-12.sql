CREATE OR REPLACE VIEW relatorio.view_historico_9anos AS
    SELECT
        historico_disciplinas.ref_ref_cod_aluno AS cod_aluno,
        historico_disciplinas.disciplina,
        (historico_por_disciplina.anos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-ano'::text))::integer AS ano_1serie,
        (historico_por_disciplina.anos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-ano'::text))::integer AS ano_2serie,
        (historico_por_disciplina.anos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-ano'::text))::integer AS ano_3serie,
        (historico_por_disciplina.anos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-ano'::text))::integer AS ano_4serie,
        (historico_por_disciplina.anos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-ano'::text))::integer AS ano_5serie,
        (historico_por_disciplina.anos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-ano'::text))::integer AS ano_6serie,
        (historico_por_disciplina.anos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-ano'::text))::integer AS ano_7serie,
        (historico_por_disciplina.anos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-ano'::text))::integer AS ano_8serie,
        (historico_por_disciplina.anos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-ano'::text))::integer AS ano_9serie,
        historico_por_disciplina.escola OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-escola'::text) AS escola_1serie,
        historico_por_disciplina.escola OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-escola'::text) AS escola_2serie,
        historico_por_disciplina.escola OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-escola'::text) AS escola_3serie,
        historico_por_disciplina.escola OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-escola'::text) AS escola_4serie,
        historico_por_disciplina.escola OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-escola'::text) AS escola_5serie,
        historico_por_disciplina.escola OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-escola'::text) AS escola_6serie,
        historico_por_disciplina.escola OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-escola'::text) AS escola_7serie,
        historico_por_disciplina.escola OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-escola'::text) AS escola_8serie,
        historico_por_disciplina.escola OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-escola'::text) AS escola_9serie,
        historico_por_disciplina.escola_cidade OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-escola_cidade'::text) AS escola_cidade_1serie,
        historico_por_disciplina.escola_cidade OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-escola_cidade'::text) AS escola_cidade_2serie,
        historico_por_disciplina.escola_cidade OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-escola_cidade'::text) AS escola_cidade_3serie,
        historico_por_disciplina.escola_cidade OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-escola_cidade'::text) AS escola_cidade_4serie,
        historico_por_disciplina.escola_cidade OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-escola_cidade'::text) AS escola_cidade_5serie,
        historico_por_disciplina.escola_cidade OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-escola_cidade'::text) AS escola_cidade_6serie,
        historico_por_disciplina.escola_cidade OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-escola_cidade'::text) AS escola_cidade_7serie,
        historico_por_disciplina.escola_cidade OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-escola_cidade'::text) AS escola_cidade_8serie,
        historico_por_disciplina.escola_cidade OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-escola_cidade'::text) AS escola_cidade_9serie,
        historico_por_disciplina.registro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-registro'::text) AS registro_1serie,
        historico_por_disciplina.registro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-registro'::text) AS registro_2serie,
        historico_por_disciplina.registro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-registro'::text) AS registro_3serie,
        historico_por_disciplina.registro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-registro'::text) AS registro_4serie,
        historico_por_disciplina.registro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-registro'::text) AS registro_5serie,
        historico_por_disciplina.registro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-registro'::text) AS registro_6serie,
        historico_por_disciplina.registro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-registro'::text) AS registro_7serie,
        historico_por_disciplina.registro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-registro'::text) AS registro_8serie,
        historico_por_disciplina.registro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-registro'::text) AS registro_9serie,
        historico_por_disciplina.livro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-livro'::text) AS livro_1serie,
        historico_por_disciplina.livro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-livro'::text) AS livro_2serie,
        historico_por_disciplina.livro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-livro'::text) AS livro_3serie,
        historico_por_disciplina.livro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-livro'::text) AS livro_4serie,
        historico_por_disciplina.livro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-livro'::text) AS livro_5serie,
        historico_por_disciplina.livro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-livro'::text) AS livro_6serie,
        historico_por_disciplina.livro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-livro'::text) AS livro_7serie,
        historico_por_disciplina.livro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-livro'::text) AS livro_8serie,
        historico_por_disciplina.livro OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-livro'::text) AS livro_9serie,
        historico_por_disciplina.folha OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-folha'::text) AS folha_1serie,
        historico_por_disciplina.folha OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-folha'::text) AS folha_2serie,
        historico_por_disciplina.folha OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-folha'::text) AS folha_3serie,
        historico_por_disciplina.folha OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-folha'::text) AS folha_4serie,
        historico_por_disciplina.folha OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-folha'::text) AS folha_5serie,
        historico_por_disciplina.folha OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-folha'::text) AS folha_6serie,
        historico_por_disciplina.folha OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-folha'::text) AS folha_7serie,
        historico_por_disciplina.folha OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-folha'::text) AS folha_8serie,
        historico_por_disciplina.folha OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-folha'::text) AS folha_9serie,
        historico_por_disciplina.escola_uf OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-escola_uf'::text) AS escola_uf_1serie,
        historico_por_disciplina.escola_uf OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-escola_uf'::text) AS escola_uf_2serie,
        historico_por_disciplina.escola_uf OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-escola_uf'::text) AS escola_uf_3serie,
        historico_por_disciplina.escola_uf OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-escola_uf'::text) AS escola_uf_4serie,
        historico_por_disciplina.escola_uf OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-escola_uf'::text) AS escola_uf_5serie,
        historico_por_disciplina.escola_uf OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-escola_uf'::text) AS escola_uf_6serie,
        historico_por_disciplina.escola_uf OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-escola_uf'::text) AS escola_uf_7serie,
        historico_por_disciplina.escola_uf OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-escola_uf'::text) AS escola_uf_8serie,
        historico_por_disciplina.escola_uf OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-escola_uf'::text) AS escola_uf_9serie,
        (historico_por_disciplina.carga_horaria OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-carga_horaria'::text))::integer AS carga_horaria1,
        (historico_por_disciplina.carga_horaria OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-carga_horaria'::text))::integer AS carga_horaria2,
        (historico_por_disciplina.carga_horaria OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-carga_horaria'::text))::integer AS carga_horaria3,
        (historico_por_disciplina.carga_horaria OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-carga_horaria'::text))::integer AS carga_horaria4,
        (historico_por_disciplina.carga_horaria OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-carga_horaria'::text))::integer AS carga_horaria5,
        (historico_por_disciplina.carga_horaria OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-carga_horaria'::text))::integer AS carga_horaria6,
        (historico_por_disciplina.carga_horaria OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-carga_horaria'::text))::integer AS carga_horaria7,
        (historico_por_disciplina.carga_horaria OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-carga_horaria'::text))::integer AS carga_horaria8,
        (historico_por_disciplina.carga_horaria OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-carga_horaria'::text))::integer AS carga_horaria9,
        (historico_por_disciplina.frequencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-frequencia'::text))::numeric AS frequencia1,
        (historico_por_disciplina.frequencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-frequencia'::text))::numeric AS frequencia2,
        (historico_por_disciplina.frequencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-frequencia'::text))::numeric AS frequencia3,
        (historico_por_disciplina.frequencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-frequencia'::text))::numeric AS frequencia4,
        (historico_por_disciplina.frequencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-frequencia'::text))::numeric AS frequencia5,
        (historico_por_disciplina.frequencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-frequencia'::text))::numeric AS frequencia6,
        (historico_por_disciplina.frequencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-frequencia'::text))::numeric AS frequencia7,
        (historico_por_disciplina.frequencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-frequencia'::text))::numeric AS frequencia8,
        (historico_por_disciplina.frequencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-frequencia'::text))::numeric AS frequencia9,
        (historico_por_disciplina.dias_letivos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-dias_letivos'::text))::integer AS dias_letivos1,
        (historico_por_disciplina.dias_letivos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-dias_letivos'::text))::integer AS dias_letivos2,
        (historico_por_disciplina.dias_letivos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-dias_letivos'::text))::integer AS dias_letivos3,
        (historico_por_disciplina.dias_letivos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-dias_letivos'::text))::integer AS dias_letivos4,
        (historico_por_disciplina.dias_letivos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-dias_letivos'::text))::integer AS dias_letivos5,
        (historico_por_disciplina.dias_letivos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-dias_letivos'::text))::integer AS dias_letivos6,
        (historico_por_disciplina.dias_letivos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-dias_letivos'::text))::integer AS dias_letivos7,
        (historico_por_disciplina.dias_letivos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-dias_letivos'::text))::integer AS dias_letivos8,
        (historico_por_disciplina.dias_letivos OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-dias_letivos'::text))::integer AS dias_letivos9,
        (historico_por_disciplina.faltas_globalizadas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-faltas_globalizadas'::text))::integer AS faltas_globalizadas1,
        (historico_por_disciplina.faltas_globalizadas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-faltas_globalizadas'::text))::integer AS faltas_globalizadas2,
        (historico_por_disciplina.faltas_globalizadas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-faltas_globalizadas'::text))::integer AS faltas_globalizadas3,
        (historico_por_disciplina.faltas_globalizadas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-faltas_globalizadas'::text))::integer AS faltas_globalizadas4,
        (historico_por_disciplina.faltas_globalizadas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-faltas_globalizadas'::text))::integer AS faltas_globalizadas5,
        (historico_por_disciplina.faltas_globalizadas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-faltas_globalizadas'::text))::integer AS faltas_globalizadas6,
        (historico_por_disciplina.faltas_globalizadas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-faltas_globalizadas'::text))::integer AS faltas_globalizadas7,
        (historico_por_disciplina.faltas_globalizadas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-faltas_globalizadas'::text))::integer AS faltas_globalizadas8,
        (historico_por_disciplina.faltas_globalizadas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-faltas_globalizadas'::text))::integer AS faltas_globalizadas9,
        historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-aprovado'::text) AS status_serie1,
        historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-aprovado'::text) AS status_serie2,
        historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-aprovado'::text) AS status_serie3,
        historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-aprovado'::text) AS status_serie4,
        historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-aprovado'::text) AS status_serie5,
        historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-aprovado'::text) AS status_serie6,
        historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-aprovado'::text) AS status_serie7,
        historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-aprovado'::text) AS status_serie8,
        historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-aprovado'::text) AS status_serie9,
        (historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-aprovado'::text)) ~~ 'Tran%'::text AS transferido1,
        (historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-aprovado'::text)) ~~ 'Tran%'::text AS transferido2,
        (historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-aprovado'::text)) ~~ 'Tran%'::text AS transferido3,
        (historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-aprovado'::text)) ~~ 'Tran%'::text AS transferido4,
        (historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-aprovado'::text)) ~~ 'Tran%'::text AS transferido5,
        (historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-aprovado'::text)) ~~ 'Tran%'::text AS transferido6,
        (historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-aprovado'::text)) ~~ 'Tran%'::text AS transferido7,
        (historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-aprovado'::text)) ~~ 'Tran%'::text AS transferido8,
        (historico_por_disciplina.aprovado OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-aprovado'::text)) ~~ 'Tran%'::text AS transferido9,
        historico_por_disciplina.nota OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-nota'::text) AS nota_1serie,
        historico_por_disciplina.nota OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-nota'::text) AS nota_2serie,
        historico_por_disciplina.nota OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-nota'::text) AS nota_3serie,
        historico_por_disciplina.nota OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-nota'::text) AS nota_4serie,
        historico_por_disciplina.nota OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-nota'::text) AS nota_5serie,
        historico_por_disciplina.nota OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-nota'::text) AS nota_6serie,
        historico_por_disciplina.nota OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-nota'::text) AS nota_7serie,
        historico_por_disciplina.nota OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-nota'::text) AS nota_8serie,
        historico_por_disciplina.nota OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-nota'::text) AS nota_9serie,
        (historico_por_disciplina.faltas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-faltas'::text))::integer AS faltas_1serie,
        (historico_por_disciplina.faltas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-faltas'::text))::integer AS faltas_2serie,
        (historico_por_disciplina.faltas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-faltas'::text))::integer AS faltas_3serie,
        (historico_por_disciplina.faltas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-faltas'::text))::integer AS faltas_4serie,
        (historico_por_disciplina.faltas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-faltas'::text))::integer AS faltas_5serie,
        (historico_por_disciplina.faltas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-faltas'::text))::integer AS faltas_6serie,
        (historico_por_disciplina.faltas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-faltas'::text))::integer AS faltas_7serie,
        (historico_por_disciplina.faltas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-faltas'::text))::integer AS faltas_8serie,
        (historico_por_disciplina.faltas OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-faltas'::text))::integer AS faltas_9serie,
        (historico_por_disciplina.carga_horaria_disciplina OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-carga_horaria_disciplina'::text))::integer AS carga_horaria_disciplina1,
        (historico_por_disciplina.carga_horaria_disciplina OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-carga_horaria_disciplina'::text))::integer AS carga_horaria_disciplina2,
        (historico_por_disciplina.carga_horaria_disciplina OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-carga_horaria_disciplina'::text))::integer AS carga_horaria_disciplina3,
        (historico_por_disciplina.carga_horaria_disciplina OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-carga_horaria_disciplina'::text))::integer AS carga_horaria_disciplina4,
        (historico_por_disciplina.carga_horaria_disciplina OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-carga_horaria_disciplina'::text))::integer AS carga_horaria_disciplina5,
        (historico_por_disciplina.carga_horaria_disciplina OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-carga_horaria_disciplina'::text))::integer AS carga_horaria_disciplina6,
        (historico_por_disciplina.carga_horaria_disciplina OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-carga_horaria_disciplina'::text))::integer AS carga_horaria_disciplina7,
        (historico_por_disciplina.carga_horaria_disciplina OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-carga_horaria_disciplina'::text))::integer AS carga_horaria_disciplina8,
        (historico_por_disciplina.carga_horaria_disciplina OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-carga_horaria_disciplina'::text))::integer AS carga_horaria_disciplina9,
        (historico_por_disciplina.dependencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '1'::text) || '-dependencia'::text))::boolean AS disciplina_dependencia1,
        (historico_por_disciplina.dependencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '2'::text) || '-dependencia'::text))::boolean AS disciplina_dependencia2,
        (historico_por_disciplina.dependencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '3'::text) || '-dependencia'::text))::boolean AS disciplina_dependencia3,
        (historico_por_disciplina.dependencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '4'::text) || '-dependencia'::text))::boolean AS disciplina_dependencia4,
        (historico_por_disciplina.dependencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '5'::text) || '-dependencia'::text))::boolean AS disciplina_dependencia5,
        (historico_por_disciplina.dependencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '6'::text) || '-dependencia'::text))::boolean AS disciplina_dependencia6,
        (historico_por_disciplina.dependencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '7'::text) || '-dependencia'::text))::boolean AS disciplina_dependencia7,
        (historico_por_disciplina.dependencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '8'::text) || '-dependencia'::text))::boolean AS disciplina_dependencia8,
        (historico_por_disciplina.dependencia OPERATOR(relatorio.->) (((historico_disciplinas.disciplina || '-'::text) || '9'::text) || '-dependencia'::text))::boolean AS disciplina_dependencia9,
        (
            SELECT m.cod_matricula
            FROM pmieducar.matricula m
            WHERE m.ano = (
                (
                    SELECT max(he.ano) AS max
                    FROM pmieducar.historico_escolar he
                    WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
                        AND he.ativo = 1
                        AND he.extra_curricular = 0
                        AND COALESCE(he.dependencia, false) = false
                        AND isnumeric("substring"(he.nm_serie::text, 1, 1))
                )
            )
            AND m.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
            AND m.ativo = 1
            AND m.aprovado = 4
            ORDER BY m.cod_matricula DESC
            LIMIT 1
        ) AS matricula_transferido,
        (
            SELECT textcat_all(tabl.obs) AS textcat_all
            FROM (
                SELECT phe.observacao AS obs
                FROM pmieducar.historico_escolar phe
                WHERE phe.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
                    AND phe.ativo = 1
                    AND phe.extra_curricular = 0
                    AND COALESCE(phe.dependencia, false) = false
                    AND isnumeric("substring"(phe.nm_serie::text, 1, 1))
                    ORDER BY phe.ano
            ) tabl
        ) AS observacao_all
    FROM pmieducar.historico_escolar
    JOIN LATERAL (
        SELECT
            historico_disciplinas_1.sequencial,
            historico_disciplinas_1.ref_ref_cod_aluno,
            historico_disciplinas_1.ref_sequencial,
            btrim(relatorio.get_texto_sem_caracter_especial(historico_disciplinas_1.nm_disciplina::character varying)::text) AS disciplina,
            historico_disciplinas_1.nota,
            historico_disciplinas_1.faltas
        FROM pmieducar.historico_disciplinas historico_disciplinas_1
    ) historico_disciplinas ON historico_escolar.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND historico_escolar.sequencial = historico_disciplinas.ref_sequencial
    JOIN LATERAL (
        SELECT
            relatorio.hstore(array_agg(disciplina.ano_key), array_agg(disciplina.ano_value)) AS anos,
            relatorio.hstore(array_agg(disciplina.escola_key), array_agg(disciplina.escola_value)) AS escola,
            relatorio.hstore(array_agg(disciplina.escola_cidade_key), array_agg(disciplina.escola_cidade_value)) AS escola_cidade,
            relatorio.hstore(array_agg(disciplina.registro_key), array_agg(disciplina.registro_value)) AS registro,
            relatorio.hstore(array_agg(disciplina.livro_key), array_agg(disciplina.livro_value)) AS livro,
            relatorio.hstore(array_agg(disciplina.folha_key), array_agg(disciplina.folha_value)) AS folha,
            relatorio.hstore(array_agg(disciplina.escola_uf_key), array_agg(disciplina.escola_uf_value)) AS escola_uf,
            relatorio.hstore(array_agg(disciplina.carga_horaria_key), array_agg(disciplina.carga_horaria_value)) AS carga_horaria,
            relatorio.hstore(array_agg(disciplina.frequencia_key), array_agg(disciplina.frequencia_value)) AS frequencia,
            relatorio.hstore(array_agg(disciplina.dias_letivos_key), array_agg(disciplina.dias_letivos_value)) AS dias_letivos,
            relatorio.hstore(array_agg(disciplina.faltas_globalizadas_key), array_agg(disciplina.faltas_globalizadas_value)) AS faltas_globalizadas,
            relatorio.hstore(array_agg(disciplina.aprovado_key), array_agg(disciplina.aprovado_value)) AS aprovado,
            relatorio.hstore(array_agg(disciplina.nota_key), array_agg(disciplina.nota_value)) AS nota,
            relatorio.hstore(array_agg(disciplina.faltas_key), array_agg(disciplina.faltas_value)) AS faltas,
            relatorio.hstore(array_agg(disciplina.carga_horaria_disciplina_key), array_agg(disciplina.carga_horaria_disciplina_value)) AS carga_horaria_disciplina,
            relatorio.hstore(array_agg(disciplina.dependencia_key), array_agg(disciplina.dependencia_value)) AS dependencia,
            disciplina.disciplina,
            disciplina.ref_cod_aluno
        FROM (
            SELECT
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-ano'::text AS ano_key,
                historico_escolar_1.ano::text AS ano_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-escola'::text AS escola_key,
                historico_escolar_1.escola::text AS escola_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-escola_cidade'::text AS escola_cidade_key,
                historico_escolar_1.escola_cidade::text AS escola_cidade_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-registro'::text AS registro_key,
                historico_escolar_1.registro::text AS registro_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-livro'::text AS livro_key,
                historico_escolar_1.livro::text AS livro_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-folha'::text AS folha_key,
                historico_escolar_1.folha::text AS folha_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-escola_uf'::text AS escola_uf_key,
                historico_escolar_1.escola_uf::text AS escola_uf_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-carga_horaria'::text AS carga_horaria_key,
                historico_escolar_1.carga_horaria::text AS carga_horaria_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-frequencia'::text AS frequencia_key,
                historico_escolar_1.frequencia::text AS frequencia_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-dias_letivos'::text AS dias_letivos_key,
                historico_escolar_1.dias_letivos::text AS dias_letivos_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-faltas_globalizadas'::text AS faltas_globalizadas_key,
                historico_escolar_1.faltas_globalizadas::text AS faltas_globalizadas_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-aprovado'::text AS aprovado_key,

                CASE historico_escolar_1.aprovado::text
                    WHEN '1'::text THEN 'Apro'::text
                    WHEN '12'::text THEN 'AprDep'::text
                    WHEN '13'::text THEN 'AprCo'::text
                    WHEN '2'::text THEN 'Repr'::text
                    WHEN '3'::text THEN 'Curs'::text
                    WHEN '4'::text THEN 'Tran'::text
                    WHEN '5'::text THEN 'Recl'::text
                    WHEN '6'::text THEN 'Aban'::text
                    WHEN '14'::text THEN 'RpFt'::text
                    WHEN '15'::text THEN 'Fal'::text
                    ELSE ''::text
                END ||
                CASE
                    WHEN historico_escolar_1.aceleracao = 1 THEN ' AC'::text
                    ELSE ''::text
                END AS aprovado_value,

                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-nota'::text AS nota_key,

                CASE
                    WHEN isnumeric(btrim(historico_disciplinas_1.nota::text)) = true AND "substring"(btrim(historico_disciplinas_1.nota::text), 1, 1)::integer >= 0 AND "substring"(btrim(historico_disciplinas_1.nota::text), 1, 1)::integer <= 9 THEN replace(historico_disciplinas_1.nota::text, '.'::text, ','::text)
                    ELSE btrim(historico_disciplinas_1.nota::text)
                END AS nota_value,

                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-carga_horaria_disciplina'::text AS carga_horaria_disciplina_key,
                historico_disciplinas_1.carga_horaria_disciplina::text AS carga_horaria_disciplina_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-faltas'::text AS faltas_key,
                historico_disciplinas_1.faltas::text AS faltas_value,
                ((historico_disciplinas_1.disciplina || '-'::text) || "substring"(historico_escolar_1.nm_serie::text, 1, 1)) || '-dependencia'::text AS dependencia_key,
                historico_disciplinas_1.dependencia::text AS dependencia_value,
                historico_disciplinas_1.disciplina,
                historico_escolar_1.ref_cod_aluno
            FROM pmieducar.historico_escolar historico_escolar_1
            JOIN LATERAL (
                SELECT
                    historico_disciplinas_2.sequencial,
                    historico_disciplinas_2.ref_ref_cod_aluno,
                    historico_disciplinas_2.ref_sequencial,
                    btrim(relatorio.get_texto_sem_caracter_especial(historico_disciplinas_2.nm_disciplina::character varying)::text) AS disciplina,
                    historico_disciplinas_2.nota,
                    historico_disciplinas_2.faltas,
                    historico_disciplinas_2.carga_horaria_disciplina,
                    historico_disciplinas_2.dependencia
                FROM pmieducar.historico_disciplinas historico_disciplinas_2
            ) historico_disciplinas_1 ON historico_escolar_1.ref_cod_aluno = historico_disciplinas_1.ref_ref_cod_aluno AND historico_escolar_1.sequencial = historico_disciplinas_1.ref_sequencial
            WHERE historico_escolar_1.extra_curricular = 0
                AND historico_escolar_1.ativo = 1
                AND COALESCE(historico_escolar_1.dependencia, false) = false
                AND isnumeric("substring"(historico_escolar_1.nm_serie::text, 1, 1))
                AND historico_disciplinas_1.ref_ref_cod_aluno = historico_disciplinas_1.ref_ref_cod_aluno
                AND historico_escolar_1.sequencial = (
                    (
                        SELECT hee.sequencial
                        FROM pmieducar.historico_escolar hee
                        WHERE "substring"(hee.nm_serie::text, 1, 1) = "substring"(historico_escolar_1.nm_serie::text, 1, 1)
                            AND hee.ref_cod_aluno = historico_escolar_1.ref_cod_aluno
                            AND hee.extra_curricular = 0
                            AND COALESCE(hee.dependencia, false) = false
                            AND isnumeric("substring"(hee.nm_serie::text, 1, 1))
                            AND hee.ativo = 1
                        ORDER BY hee.ano DESC, (relatorio.prioridade_historico(hee.aprovado::numeric))
                        LIMIT 1
                    )
                )
            GROUP BY
                historico_disciplinas_1.disciplina,
                historico_escolar_1.ano,
                historico_escolar_1.escola,
                historico_escolar_1.escola_cidade,
                historico_escolar_1.registro,
                historico_escolar_1.livro,
                historico_escolar_1.folha,
                historico_escolar_1.escola_uf,
                historico_escolar_1.carga_horaria,
                historico_escolar_1.frequencia,
                historico_escolar_1.dias_letivos,
                historico_escolar_1.faltas_globalizadas,
                historico_escolar_1.aprovado,
                historico_escolar_1.nm_serie,
                historico_escolar_1.aceleracao,
                historico_disciplinas_1.nota,
                historico_disciplinas_1.faltas,
                historico_disciplinas_1.carga_horaria_disciplina,
                historico_disciplinas_1.dependencia,
                historico_escolar_1.ref_cod_aluno
            ORDER BY
                historico_escolar_1.ano DESC,
                (relatorio.prioridade_historico(historico_escolar_1.aprovado::numeric))
        ) disciplina
        GROUP BY
            disciplina.disciplina,
            disciplina.ref_cod_aluno
    ) historico_por_disciplina ON historico_por_disciplina.ref_cod_aluno = historico_escolar.ref_cod_aluno AND historico_por_disciplina.disciplina = historico_disciplinas.disciplina
    WHERE historico_escolar.extra_curricular = 0
        AND COALESCE(historico_escolar.dependencia, false) = false
        AND isnumeric("substring"(historico_escolar.nm_serie::text, 1, 1))
        AND historico_escolar.ativo = 1
    GROUP BY
        historico_disciplinas.disciplina,
        historico_disciplinas.ref_ref_cod_aluno,
        historico_por_disciplina.anos,
        historico_por_disciplina.escola,
        historico_por_disciplina.escola_cidade,
        historico_por_disciplina.registro,
        historico_por_disciplina.livro,
        historico_por_disciplina.folha,
        historico_por_disciplina.escola_uf,
        historico_por_disciplina.carga_horaria,
        historico_por_disciplina.frequencia,
        historico_por_disciplina.dias_letivos,
        historico_por_disciplina.faltas_globalizadas,
        historico_por_disciplina.aprovado,
        historico_por_disciplina.nota,
        historico_por_disciplina.faltas,
        historico_por_disciplina.carga_horaria_disciplina,
        historico_por_disciplina.dependencia
    ORDER BY historico_disciplinas.disciplina;
