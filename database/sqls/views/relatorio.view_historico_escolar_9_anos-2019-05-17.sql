SET search_path = relatorio, pmieducar, public, pg_catalog;

DROP VIEW IF EXISTS relatorio.view_historico_9anos;

CREATE OR REPLACE VIEW relatorio.view_historico_9anos AS
SELECT
    historico_disciplinas.ref_ref_cod_aluno AS cod_aluno,
    historico_disciplinas.disciplina,
    (historico_por_disciplina.anos -> (historico_disciplinas.disciplina || '-' || '1' || '-ano'))::INT ano_1serie,
    (historico_por_disciplina.anos -> (historico_disciplinas.disciplina || '-' || '2' || '-ano'))::INT ano_2serie,
    (historico_por_disciplina.anos -> (historico_disciplinas.disciplina || '-' || '3' || '-ano'))::INT ano_3serie,
    (historico_por_disciplina.anos -> (historico_disciplinas.disciplina || '-' || '4' || '-ano'))::INT ano_4serie,
    (historico_por_disciplina.anos -> (historico_disciplinas.disciplina || '-' || '5' || '-ano'))::INT ano_5serie,
    (historico_por_disciplina.anos -> (historico_disciplinas.disciplina || '-' || '6' || '-ano'))::INT ano_6serie,
    (historico_por_disciplina.anos -> (historico_disciplinas.disciplina || '-' || '7' || '-ano'))::INT ano_7serie,
    (historico_por_disciplina.anos -> (historico_disciplinas.disciplina || '-' || '8' || '-ano'))::INT ano_8serie,
    (historico_por_disciplina.anos -> (historico_disciplinas.disciplina || '-' || '9' || '-ano'))::INT ano_9serie,
    historico_por_disciplina.escola -> (historico_disciplinas.disciplina || '-' || '1' || '-escola') escola_1serie,
    historico_por_disciplina.escola -> (historico_disciplinas.disciplina || '-' || '2' || '-escola') escola_2serie,
    historico_por_disciplina.escola -> (historico_disciplinas.disciplina || '-' || '3' || '-escola') escola_3serie,
    historico_por_disciplina.escola -> (historico_disciplinas.disciplina || '-' || '4' || '-escola') escola_4serie,
    historico_por_disciplina.escola -> (historico_disciplinas.disciplina || '-' || '5' || '-escola') escola_5serie,
    historico_por_disciplina.escola -> (historico_disciplinas.disciplina || '-' || '6' || '-escola') escola_6serie,
    historico_por_disciplina.escola -> (historico_disciplinas.disciplina || '-' || '7' || '-escola') escola_7serie,
    historico_por_disciplina.escola -> (historico_disciplinas.disciplina || '-' || '8' || '-escola') escola_8serie,
    historico_por_disciplina.escola -> (historico_disciplinas.disciplina || '-' || '9' || '-escola') escola_9serie,
    historico_por_disciplina.escola_cidade -> (historico_disciplinas.disciplina || '-' || '1' || '-escola_cidade') escola_cidade_1serie,
    historico_por_disciplina.escola_cidade -> (historico_disciplinas.disciplina || '-' || '2' || '-escola_cidade') escola_cidade_2serie,
    historico_por_disciplina.escola_cidade -> (historico_disciplinas.disciplina || '-' || '3' || '-escola_cidade') escola_cidade_3serie,
    historico_por_disciplina.escola_cidade -> (historico_disciplinas.disciplina || '-' || '4' || '-escola_cidade') escola_cidade_4serie,
    historico_por_disciplina.escola_cidade -> (historico_disciplinas.disciplina || '-' || '5' || '-escola_cidade') escola_cidade_5serie,
    historico_por_disciplina.escola_cidade -> (historico_disciplinas.disciplina || '-' || '6' || '-escola_cidade') escola_cidade_6serie,
    historico_por_disciplina.escola_cidade -> (historico_disciplinas.disciplina || '-' || '7' || '-escola_cidade') escola_cidade_7serie,
    historico_por_disciplina.escola_cidade -> (historico_disciplinas.disciplina || '-' || '8' || '-escola_cidade') escola_cidade_8serie,
    historico_por_disciplina.escola_cidade -> (historico_disciplinas.disciplina || '-' || '9' || '-escola_cidade') escola_cidade_9serie,
    historico_por_disciplina.registro -> (historico_disciplinas.disciplina || '-' || '1' || '-registro') registro_1serie,
    historico_por_disciplina.registro -> (historico_disciplinas.disciplina || '-' || '2' || '-registro') registro_2serie,
    historico_por_disciplina.registro -> (historico_disciplinas.disciplina || '-' || '3' || '-registro') registro_3serie,
    historico_por_disciplina.registro -> (historico_disciplinas.disciplina || '-' || '4' || '-registro') registro_4serie,
    historico_por_disciplina.registro -> (historico_disciplinas.disciplina || '-' || '5' || '-registro') registro_5serie,
    historico_por_disciplina.registro -> (historico_disciplinas.disciplina || '-' || '6' || '-registro') registro_6serie,
    historico_por_disciplina.registro -> (historico_disciplinas.disciplina || '-' || '7' || '-registro') registro_7serie,
    historico_por_disciplina.registro -> (historico_disciplinas.disciplina || '-' || '8' || '-registro') registro_8serie,
    historico_por_disciplina.registro -> (historico_disciplinas.disciplina || '-' || '9' || '-registro') registro_9serie,
    historico_por_disciplina.livro -> (historico_disciplinas.disciplina || '-' || '1' || '-livro') livro_1serie,
    historico_por_disciplina.livro -> (historico_disciplinas.disciplina || '-' || '2' || '-livro') livro_2serie,
    historico_por_disciplina.livro -> (historico_disciplinas.disciplina || '-' || '3' || '-livro') livro_3serie,
    historico_por_disciplina.livro -> (historico_disciplinas.disciplina || '-' || '4' || '-livro') livro_4serie,
    historico_por_disciplina.livro -> (historico_disciplinas.disciplina || '-' || '5' || '-livro') livro_5serie,
    historico_por_disciplina.livro -> (historico_disciplinas.disciplina || '-' || '6' || '-livro') livro_6serie,
    historico_por_disciplina.livro -> (historico_disciplinas.disciplina || '-' || '7' || '-livro') livro_7serie,
    historico_por_disciplina.livro -> (historico_disciplinas.disciplina || '-' || '8' || '-livro') livro_8serie,
    historico_por_disciplina.livro -> (historico_disciplinas.disciplina || '-' || '9' || '-livro') livro_9serie,
    historico_por_disciplina.folha -> (historico_disciplinas.disciplina || '-' || '1' || '-folha') folha_1serie,
    historico_por_disciplina.folha -> (historico_disciplinas.disciplina || '-' || '2' || '-folha') folha_2serie,
    historico_por_disciplina.folha -> (historico_disciplinas.disciplina || '-' || '3' || '-folha') folha_3serie,
    historico_por_disciplina.folha -> (historico_disciplinas.disciplina || '-' || '4' || '-folha') folha_4serie,
    historico_por_disciplina.folha -> (historico_disciplinas.disciplina || '-' || '5' || '-folha') folha_5serie,
    historico_por_disciplina.folha -> (historico_disciplinas.disciplina || '-' || '6' || '-folha') folha_6serie,
    historico_por_disciplina.folha -> (historico_disciplinas.disciplina || '-' || '7' || '-folha') folha_7serie,
    historico_por_disciplina.folha -> (historico_disciplinas.disciplina || '-' || '8' || '-folha') folha_8serie,
    historico_por_disciplina.folha -> (historico_disciplinas.disciplina || '-' || '9' || '-folha') folha_9serie,
    historico_por_disciplina.escola_uf -> (historico_disciplinas.disciplina || '-' || '1' || '-escola_uf') escola_uf_1serie,
    historico_por_disciplina.escola_uf -> (historico_disciplinas.disciplina || '-' || '2' || '-escola_uf') escola_uf_2serie,
    historico_por_disciplina.escola_uf -> (historico_disciplinas.disciplina || '-' || '3' || '-escola_uf') escola_uf_3serie,
    historico_por_disciplina.escola_uf -> (historico_disciplinas.disciplina || '-' || '4' || '-escola_uf') escola_uf_4serie,
    historico_por_disciplina.escola_uf -> (historico_disciplinas.disciplina || '-' || '5' || '-escola_uf') escola_uf_5serie,
    historico_por_disciplina.escola_uf -> (historico_disciplinas.disciplina || '-' || '6' || '-escola_uf') escola_uf_6serie,
    historico_por_disciplina.escola_uf -> (historico_disciplinas.disciplina || '-' || '7' || '-escola_uf') escola_uf_7serie,
    historico_por_disciplina.escola_uf -> (historico_disciplinas.disciplina || '-' || '8' || '-escola_uf') escola_uf_8serie,
    historico_por_disciplina.escola_uf -> (historico_disciplinas.disciplina || '-' || '9' || '-escola_uf') escola_uf_9serie,
    (historico_por_disciplina.carga_horaria -> (historico_disciplinas.disciplina || '-' || '1' || '-carga_horaria'))::INTEGER carga_horaria1,
    (historico_por_disciplina.carga_horaria -> (historico_disciplinas.disciplina || '-' || '2' || '-carga_horaria'))::INTEGER carga_horaria2,
    (historico_por_disciplina.carga_horaria -> (historico_disciplinas.disciplina || '-' || '3' || '-carga_horaria'))::INTEGER carga_horaria3,
    (historico_por_disciplina.carga_horaria -> (historico_disciplinas.disciplina || '-' || '4' || '-carga_horaria'))::INTEGER carga_horaria4,
    (historico_por_disciplina.carga_horaria -> (historico_disciplinas.disciplina || '-' || '5' || '-carga_horaria'))::INTEGER carga_horaria5,
    (historico_por_disciplina.carga_horaria -> (historico_disciplinas.disciplina || '-' || '6' || '-carga_horaria'))::INTEGER carga_horaria6,
    (historico_por_disciplina.carga_horaria -> (historico_disciplinas.disciplina || '-' || '7' || '-carga_horaria'))::INTEGER carga_horaria7,
    (historico_por_disciplina.carga_horaria -> (historico_disciplinas.disciplina || '-' || '8' || '-carga_horaria'))::INTEGER carga_horaria8,
    (historico_por_disciplina.carga_horaria -> (historico_disciplinas.disciplina || '-' || '9' || '-carga_horaria'))::INTEGER carga_horaria9,
    (historico_por_disciplina.frequencia -> (historico_disciplinas.disciplina || '-' || '1' || '-frequencia'))::NUMERIC frequencia1,
    (historico_por_disciplina.frequencia -> (historico_disciplinas.disciplina || '-' || '2' || '-frequencia'))::NUMERIC frequencia2,
    (historico_por_disciplina.frequencia -> (historico_disciplinas.disciplina || '-' || '3' || '-frequencia'))::NUMERIC frequencia3,
    (historico_por_disciplina.frequencia -> (historico_disciplinas.disciplina || '-' || '4' || '-frequencia'))::NUMERIC frequencia4,
    (historico_por_disciplina.frequencia -> (historico_disciplinas.disciplina || '-' || '5' || '-frequencia'))::NUMERIC frequencia5,
    (historico_por_disciplina.frequencia -> (historico_disciplinas.disciplina || '-' || '6' || '-frequencia'))::NUMERIC frequencia6,
    (historico_por_disciplina.frequencia -> (historico_disciplinas.disciplina || '-' || '7' || '-frequencia'))::NUMERIC frequencia7,
    (historico_por_disciplina.frequencia -> (historico_disciplinas.disciplina || '-' || '8' || '-frequencia'))::NUMERIC frequencia8,
    (historico_por_disciplina.frequencia -> (historico_disciplinas.disciplina || '-' || '9' || '-frequencia'))::NUMERIC frequencia9,
    (historico_por_disciplina.dias_letivos -> (historico_disciplinas.disciplina || '-' || '1' || '-dias_letivos'))::INT dias_letivos1,
    (historico_por_disciplina.dias_letivos -> (historico_disciplinas.disciplina || '-' || '2' || '-dias_letivos'))::INT dias_letivos2,
    (historico_por_disciplina.dias_letivos -> (historico_disciplinas.disciplina || '-' || '3' || '-dias_letivos'))::INT dias_letivos3,
    (historico_por_disciplina.dias_letivos -> (historico_disciplinas.disciplina || '-' || '4' || '-dias_letivos'))::INT dias_letivos4,
    (historico_por_disciplina.dias_letivos -> (historico_disciplinas.disciplina || '-' || '5' || '-dias_letivos'))::INT dias_letivos5,
    (historico_por_disciplina.dias_letivos -> (historico_disciplinas.disciplina || '-' || '6' || '-dias_letivos'))::INT dias_letivos6,
    (historico_por_disciplina.dias_letivos -> (historico_disciplinas.disciplina || '-' || '7' || '-dias_letivos'))::INT dias_letivos7,
    (historico_por_disciplina.dias_letivos -> (historico_disciplinas.disciplina || '-' || '8' || '-dias_letivos'))::INT dias_letivos8,
    (historico_por_disciplina.dias_letivos -> (historico_disciplinas.disciplina || '-' || '9' || '-dias_letivos'))::INT dias_letivos9,
    (historico_por_disciplina.faltas_globalizadas -> (historico_disciplinas.disciplina || '-' || '1' || '-faltas_globalizadas'))::INT faltas_globalizadas1,
    (historico_por_disciplina.faltas_globalizadas -> (historico_disciplinas.disciplina || '-' || '2' || '-faltas_globalizadas'))::INT faltas_globalizadas2,
    (historico_por_disciplina.faltas_globalizadas -> (historico_disciplinas.disciplina || '-' || '3' || '-faltas_globalizadas'))::INT faltas_globalizadas3,
    (historico_por_disciplina.faltas_globalizadas -> (historico_disciplinas.disciplina || '-' || '4' || '-faltas_globalizadas'))::INT faltas_globalizadas4,
    (historico_por_disciplina.faltas_globalizadas -> (historico_disciplinas.disciplina || '-' || '5' || '-faltas_globalizadas'))::INT faltas_globalizadas5,
    (historico_por_disciplina.faltas_globalizadas -> (historico_disciplinas.disciplina || '-' || '6' || '-faltas_globalizadas'))::INT faltas_globalizadas6,
    (historico_por_disciplina.faltas_globalizadas -> (historico_disciplinas.disciplina || '-' || '7' || '-faltas_globalizadas'))::INT faltas_globalizadas7,
    (historico_por_disciplina.faltas_globalizadas -> (historico_disciplinas.disciplina || '-' || '8' || '-faltas_globalizadas'))::INT faltas_globalizadas8,
    (historico_por_disciplina.faltas_globalizadas -> (historico_disciplinas.disciplina || '-' || '9' || '-faltas_globalizadas'))::INT faltas_globalizadas9,
    historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '1' || '-aprovado') status_serie1,
    historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '2' || '-aprovado') status_serie2,
    historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '3' || '-aprovado') status_serie3,
    historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '4' || '-aprovado') status_serie4,
    historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '5' || '-aprovado') status_serie5,
    historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '6' || '-aprovado') status_serie6,
    historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '7' || '-aprovado') status_serie7,
    historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '8' || '-aprovado') status_serie8,
    historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '9' || '-aprovado') status_serie9,
    (historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '1' || '-aprovado') LIKE 'Tran%')::BOOLEAN transferido1,
    (historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '2' || '-aprovado') LIKE 'Tran%')::BOOLEAN transferido2,
    (historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '3' || '-aprovado') LIKE 'Tran%')::BOOLEAN transferido3,
    (historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '4' || '-aprovado') LIKE 'Tran%')::BOOLEAN transferido4,
    (historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '5' || '-aprovado') LIKE 'Tran%')::BOOLEAN transferido5,
    (historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '6' || '-aprovado') LIKE 'Tran%')::BOOLEAN transferido6,
    (historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '7' || '-aprovado') LIKE 'Tran%')::BOOLEAN transferido7,
    (historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '8' || '-aprovado') LIKE 'Tran%')::BOOLEAN transferido8,
    (historico_por_disciplina.aprovado -> (historico_disciplinas.disciplina || '-' || '9' || '-aprovado') LIKE 'Tran%')::BOOLEAN transferido9,
    historico_por_disciplina.nota -> (historico_disciplinas.disciplina || '-' || '1' || '-nota') nota_1serie,
    historico_por_disciplina.nota -> (historico_disciplinas.disciplina || '-' || '2' || '-nota') nota_2serie,
    historico_por_disciplina.nota -> (historico_disciplinas.disciplina || '-' || '3' || '-nota') nota_3serie,
    historico_por_disciplina.nota -> (historico_disciplinas.disciplina || '-' || '4' || '-nota') nota_4serie,
    historico_por_disciplina.nota -> (historico_disciplinas.disciplina || '-' || '5' || '-nota') nota_5serie,
    historico_por_disciplina.nota -> (historico_disciplinas.disciplina || '-' || '6' || '-nota') nota_6serie,
    historico_por_disciplina.nota -> (historico_disciplinas.disciplina || '-' || '7' || '-nota') nota_7serie,
    historico_por_disciplina.nota -> (historico_disciplinas.disciplina || '-' || '8' || '-nota') nota_8serie,
    historico_por_disciplina.nota -> (historico_disciplinas.disciplina || '-' || '9' || '-nota') nota_9serie,
    (historico_por_disciplina.faltas -> (historico_disciplinas.disciplina || '-' || '1' || '-faltas'))::INT faltas_1serie,
    (historico_por_disciplina.faltas -> (historico_disciplinas.disciplina || '-' || '2' || '-faltas'))::INT faltas_2serie,
    (historico_por_disciplina.faltas -> (historico_disciplinas.disciplina || '-' || '3' || '-faltas'))::INT faltas_3serie,
    (historico_por_disciplina.faltas -> (historico_disciplinas.disciplina || '-' || '4' || '-faltas'))::INT faltas_4serie,
    (historico_por_disciplina.faltas -> (historico_disciplinas.disciplina || '-' || '5' || '-faltas'))::INT faltas_5serie,
    (historico_por_disciplina.faltas -> (historico_disciplinas.disciplina || '-' || '6' || '-faltas'))::INT faltas_6serie,
    (historico_por_disciplina.faltas -> (historico_disciplinas.disciplina || '-' || '7' || '-faltas'))::INT faltas_7serie,
    (historico_por_disciplina.faltas -> (historico_disciplinas.disciplina || '-' || '8' || '-faltas'))::INT faltas_8serie,
    (historico_por_disciplina.faltas -> (historico_disciplinas.disciplina || '-' || '9' || '-faltas'))::INT faltas_9serie,
    (historico_por_disciplina.carga_horaria_disciplina -> (historico_disciplinas.disciplina || '-' || '1' || '-carga_horaria_disciplina'))::INTEGER carga_horaria_disciplina1,
    (historico_por_disciplina.carga_horaria_disciplina -> (historico_disciplinas.disciplina || '-' || '2' || '-carga_horaria_disciplina'))::INTEGER carga_horaria_disciplina2,
    (historico_por_disciplina.carga_horaria_disciplina -> (historico_disciplinas.disciplina || '-' || '3' || '-carga_horaria_disciplina'))::INTEGER carga_horaria_disciplina3,
    (historico_por_disciplina.carga_horaria_disciplina -> (historico_disciplinas.disciplina || '-' || '4' || '-carga_horaria_disciplina'))::INTEGER carga_horaria_disciplina4,
    (historico_por_disciplina.carga_horaria_disciplina -> (historico_disciplinas.disciplina || '-' || '5' || '-carga_horaria_disciplina'))::INTEGER carga_horaria_disciplina5,
    (historico_por_disciplina.carga_horaria_disciplina -> (historico_disciplinas.disciplina || '-' || '6' || '-carga_horaria_disciplina'))::INTEGER carga_horaria_disciplina6,
    (historico_por_disciplina.carga_horaria_disciplina -> (historico_disciplinas.disciplina || '-' || '7' || '-carga_horaria_disciplina'))::INTEGER carga_horaria_disciplina7,
    (historico_por_disciplina.carga_horaria_disciplina -> (historico_disciplinas.disciplina || '-' || '8' || '-carga_horaria_disciplina'))::INTEGER carga_horaria_disciplina8,
    (historico_por_disciplina.carga_horaria_disciplina -> (historico_disciplinas.disciplina || '-' || '9' || '-carga_horaria_disciplina'))::INTEGER carga_horaria_disciplina9,
    (historico_por_disciplina.dependencia -> (historico_disciplinas.disciplina || '-' || '1' || '-dependencia'))::BOOLEAN disciplina_dependencia1,
    (historico_por_disciplina.dependencia -> (historico_disciplinas.disciplina || '-' || '2' || '-dependencia'))::BOOLEAN disciplina_dependencia2,
    (historico_por_disciplina.dependencia -> (historico_disciplinas.disciplina || '-' || '3' || '-dependencia'))::BOOLEAN disciplina_dependencia3,
    (historico_por_disciplina.dependencia -> (historico_disciplinas.disciplina || '-' || '4' || '-dependencia'))::BOOLEAN disciplina_dependencia4,
    (historico_por_disciplina.dependencia -> (historico_disciplinas.disciplina || '-' || '5' || '-dependencia'))::BOOLEAN disciplina_dependencia5,
    (historico_por_disciplina.dependencia -> (historico_disciplinas.disciplina || '-' || '6' || '-dependencia'))::BOOLEAN disciplina_dependencia6,
    (historico_por_disciplina.dependencia -> (historico_disciplinas.disciplina || '-' || '7' || '-dependencia'))::BOOLEAN disciplina_dependencia7,
    (historico_por_disciplina.dependencia -> (historico_disciplinas.disciplina || '-' || '8' || '-dependencia'))::BOOLEAN disciplina_dependencia8,
    (historico_por_disciplina.dependencia -> (historico_disciplinas.disciplina || '-' || '9' || '-dependencia'))::BOOLEAN disciplina_dependencia9,
    (SELECT m.cod_matricula
       FROM pmieducar.matricula m
      WHERE m.ano = (SELECT MAX(he.ano)
                       FROM pmieducar.historico_escolar he
                      WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
                        AND he.ativo = 1
                        AND he.extra_curricular = 0
                        AND coalesce(he.dependencia, false) = false
                        AND isnumeric(substring(he.nm_serie::TEXT, 1, 1)))
                        AND m.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
                        AND m.ativo = 1
                        AND m.aprovado = 4
                      ORDER BY m.cod_matricula DESC
                      LIMIT 1) AS matricula_transferido,
    (SELECT textcat_all(obs)
       FROM (SELECT observacao AS obs
               FROM pmieducar.historico_escolar phe
              WHERE phe.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
                AND phe.ativo = 1
                AND phe.extra_curricular = 0
                AND coalesce(phe.dependencia, false) = false
                AND isnumeric(substring(phe.nm_serie::TEXT, 1, 1))
                ORDER BY phe.ano)tabl) AS observacao_all
    FROM pmieducar.historico_escolar
    JOIN LATERAL (SELECT historico_disciplinas.sequencial,
                         historico_disciplinas.ref_ref_cod_aluno,
                         historico_disciplinas.ref_sequencial,
                         btrim(relatorio.get_texto_sem_caracter_especial(historico_disciplinas.nm_disciplina::text::character varying)::text) AS disciplina,
                         historico_disciplinas.nota,
                         historico_disciplinas.faltas
                    FROM pmieducar.historico_disciplinas historico_disciplinas) historico_disciplinas ON (historico_escolar.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
                                                                                                     AND historico_escolar.sequencial = historico_disciplinas.ref_sequencial)
    JOIN LATERAL (SELECT hstore(array_agg(ano_key),
                         array_agg(ano_value)) anos,
                         hstore(array_agg(escola_key),
                         array_agg(escola_value)) escola,
                         hstore(array_agg(escola_cidade_key),
                         array_agg(escola_cidade_value)) escola_cidade,
                         hstore(array_agg(registro_key),
                         array_agg(registro_value)) registro,
                         hstore(array_agg(livro_key),
                         array_agg(livro_value)) livro,
                         hstore(array_agg(folha_key),
                         array_agg(folha_value)) folha,
                         hstore(array_agg(escola_uf_key),
                         array_agg(escola_uf_value)) escola_uf,
                         hstore(array_agg(carga_horaria_key),
                         array_agg(carga_horaria_value)) carga_horaria,
                         hstore(array_agg(frequencia_key),
                         array_agg(frequencia_value)) frequencia,
                         hstore(array_agg(dias_letivos_key),
                         array_agg(dias_letivos_value)) dias_letivos,
                         hstore(array_agg(faltas_globalizadas_key),
                         array_agg(faltas_globalizadas_value)) faltas_globalizadas,
                         hstore(array_agg(aprovado_key),
                         array_agg(aprovado_value)) aprovado,
                         hstore(array_agg(nota_key),
                         array_agg(nota_value)) nota,
                         hstore(array_agg(faltas_key),
                         array_agg(faltas_value)) faltas,
                         hstore(array_agg(carga_horaria_disciplina_key),
                         array_agg(carga_horaria_disciplina_value)) carga_horaria_disciplina,
                         hstore(array_agg(dependencia_key),
                         array_agg(dependencia_value)) dependencia,
                         disciplina,
                         ref_cod_aluno
                    FROM (SELECT (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-ano') AS ano_key,
                                 historico_escolar.ano::TEXT ano_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-escola') AS escola_key,
                                 historico_escolar.escola::TEXT escola_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-escola_cidade') AS escola_cidade_key,
                                 historico_escolar.escola_cidade::TEXT escola_cidade_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-registro') AS registro_key,
                                 historico_escolar.registro::TEXT registro_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-livro') AS livro_key,
                                 historico_escolar.livro::TEXT livro_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-folha') AS folha_key,
                                 historico_escolar.folha::TEXT folha_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-escola_uf') AS escola_uf_key,
                                 historico_escolar.escola_uf::TEXT escola_uf_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-carga_horaria') AS carga_horaria_key,
                                 historico_escolar.carga_horaria::TEXT carga_horaria_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-frequencia') AS frequencia_key,
                                 historico_escolar.frequencia::TEXT frequencia_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-dias_letivos') AS dias_letivos_key,
                                 historico_escolar.dias_letivos::TEXT dias_letivos_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-faltas_globalizadas') AS faltas_globalizadas_key,
                                 historico_escolar.faltas_globalizadas::TEXT faltas_globalizadas_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-aprovado') AS aprovado_key,
                                 CASE historico_escolar.aprovado::TEXT
                                    WHEN '1'  THEN 'Apro'
                                    WHEN '12' THEN 'AprDep'
                                    WHEN '13' THEN 'AprCo'
                                    WHEN '2'  THEN 'Repr'
                                    WHEN '3'  THEN 'Curs'
                                    WHEN '4'  THEN 'Tran'
                                    WHEN '5'  THEN 'Recl'
                                    WHEN '6'  THEN 'Aban'
                                    WHEN '14' THEN 'RpFt'
                                    WHEN '15' THEN 'Fal'
                                    ELSE ''
                                 END ||
                                 CASE WHEN historico_escolar.aceleracao = '1' THEN ' AC'
                                      ELSE ''
                                 END AS aprovado_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-nota') AS nota_key,
                                 CASE WHEN ISNUMERIC(btrim(nota)) = TRUE
                                       AND substring(btrim(nota), 1, 1)::INT BETWEEN 0 AND 9
                                      THEN replace(nota::TEXT, '.'::TEXT, ','::TEXT)
                                      ELSE btrim(nota)
                                 END AS nota_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-carga_horaria_disciplina') AS carga_horaria_disciplina_key,
                                 carga_horaria_disciplina::TEXT AS carga_horaria_disciplina_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-faltas') AS faltas_key,
                                 historico_disciplinas.faltas::TEXT AS faltas_value,
                                 (historico_disciplinas.disciplina || '-' || substring(historico_escolar.nm_serie::TEXT, 1, 1) || '-dependencia') AS dependencia_key,
                                 historico_disciplinas.dependencia::TEXT AS dependencia_value,
                                 historico_disciplinas.disciplina disciplina,
                                 historico_escolar.ref_cod_aluno
                            FROM pmieducar.historico_escolar
                            JOIN LATERAL (SELECT historico_disciplinas.sequencial,
                                                 historico_disciplinas.ref_ref_cod_aluno,
                                                 historico_disciplinas.ref_sequencial,
                                                 btrim(relatorio.get_texto_sem_caracter_especial(historico_disciplinas.nm_disciplina::TEXT::character varying)::TEXT) AS disciplina,
                                                 historico_disciplinas.nota,
                                                 historico_disciplinas.faltas,
                                                 carga_horaria_disciplina,
                                                 dependencia
                                            FROM pmieducar.historico_disciplinas historico_disciplinas) historico_disciplinas ON (historico_escolar.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
                                                                                                                             AND historico_escolar.sequencial = historico_disciplinas.ref_sequencial)
                                           WHERE historico_escolar.extra_curricular = 0
                                             AND historico_escolar.ativo = 1
                                             AND coalesce(historico_escolar.dependencia, false) = false
                                             AND isnumeric(substring(historico_escolar.nm_serie::TEXT, 1, 1))
                                             AND historico_disciplinas.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
                                             AND historico_escolar.sequencial = (SELECT sequencial
                                            FROM pmieducar.historico_escolar hee
                                           WHERE substring(hee.nm_serie::text, 1, 1) = substring(historico_escolar.nm_serie::text, 1, 1)
                                             AND hee.ref_cod_aluno = historico_escolar.ref_cod_aluno
                                             AND hee.extra_curricular = 0
                                             AND coalesce(hee.dependencia, false) = false
                                             AND isnumeric(substring(hee.nm_serie::TEXT, 1, 1))
                                             AND hee.ativo = 1
                                        ORDER BY ano DESC, relatorio.prioridade_historico(hee.aprovado) ASC LIMIT 1)
                                        GROUP BY historico_disciplinas.disciplina,
                                                 historico_escolar.ano,
                                                 historico_escolar.escola,
                                                 historico_escolar.escola_cidade,
                                                 historico_escolar.registro,
                                                 historico_escolar.livro,
                                                 historico_escolar.folha,
                                                 historico_escolar.escola_uf,
                                                 historico_escolar.carga_horaria,
                                                 historico_escolar.frequencia,
                                                 historico_escolar.dias_letivos,
                                                 historico_escolar.faltas_globalizadas,
                                                 historico_escolar.aprovado,
                                                 historico_escolar.nm_serie,
                                                 historico_escolar.aprovado,
                                                 historico_escolar.aceleracao,
                                                 historico_disciplinas.nota,
                                                 historico_disciplinas.faltas,
                                                 historico_disciplinas.carga_horaria_disciplina,
                                                 historico_disciplinas.dependencia,
                                                 ref_cod_aluno
                                        ORDER BY historico_escolar.ano DESC,
                                                 relatorio.prioridade_historico(historico_escolar.aprovado) ASC) disciplina
                GROUP BY disciplina, ref_cod_aluno) historico_por_disciplina ON (historico_por_disciplina.ref_cod_aluno = historico_escolar.ref_cod_aluno
                                                                            AND historico_por_disciplina.disciplina = historico_disciplinas.disciplina)
                   WHERE historico_escolar.extra_curricular = 0
                     AND coalesce(historico_escolar.dependencia, false) = false
                     AND isnumeric(substring(historico_escolar.nm_serie::TEXT, 1, 1))
                     AND historico_escolar.ativo = 1
                GROUP BY historico_disciplinas.disciplina,
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
