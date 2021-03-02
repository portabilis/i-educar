CREATE OR REPLACE VIEW relatorio.view_historico_9anos_extra_curricular AS
SELECT historico_disciplinas.ref_ref_cod_aluno AS cod_aluno,
       fcn_upper(historico_disciplinas.nm_disciplina::text) AS disciplina,
       ( SELECT
             CASE
                 WHEN "substring"(btrim(hd.nota::text), 1, 1) <> 0::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 1::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 2::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 3::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 4::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 5::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 6::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 7::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 8::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 9::text THEN replace(hd.nota::text, '.'::text, ','::text)
                 WHEN to_number(btrim(hd.nota::text), '999'::text) > 10::numeric AND to_number(btrim(hd.nota::text), '999'::text) <= 20::numeric THEN replace(btrim(hd.nota::text), '.'::text, ','::text)
                 ELSE replace("substring"(btrim(hd.nota::text), 1, 4), '.'::text, ','::text)
                 END AS replace
         FROM pmieducar.historico_disciplinas hd
                  JOIN pmieducar.historico_escolar historico_escolar_1 ON historico_escolar_1.ref_cod_aluno = hd.ref_ref_cod_aluno AND historico_escolar_1.sequencial = hd.ref_sequencial
         WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND fcn_upper(hd.nm_disciplina::text) = fcn_upper(hd.nm_disciplina::text) AND historico_escolar_1.ativo = 1 AND historico_escolar_1.extra_curricular = 1 AND historico_escolar_1.ano = (( SELECT he.ano
                                                                                                                                                                                                                                                                        FROM pmieducar.historico_escolar he
                                                                                                                                                                                                                                                                        WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                                                                                                                                                 FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                                                                                                                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 1::text
                                                                                                                                                                                                                                                                        LIMIT 1))
         LIMIT 1) AS nota_1serie,
       ( SELECT
             CASE
                 WHEN "substring"(btrim(hd.nota::text), 1, 1) <> 0::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 1::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 2::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 3::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 4::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 5::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 6::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 7::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 8::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 9::text THEN replace(hd.nota::text, '.'::text, ','::text)
                 WHEN to_number(btrim(hd.nota::text), '999'::text) > 10::numeric AND to_number(btrim(hd.nota::text), '999'::text) <= 20::numeric THEN replace(btrim(hd.nota::text), '.'::text, ','::text)
                 ELSE replace("substring"(btrim(hd.nota::text), 1, 4), '.'::text, ','::text)
                 END AS replace
         FROM pmieducar.historico_disciplinas hd
                  JOIN pmieducar.historico_escolar historico_escolar_1 ON historico_escolar_1.ref_cod_aluno = hd.ref_ref_cod_aluno AND historico_escolar_1.sequencial = hd.ref_sequencial
         WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND fcn_upper(hd.nm_disciplina::text) = fcn_upper(hd.nm_disciplina::text) AND historico_escolar_1.ativo = 1 AND historico_escolar_1.extra_curricular = 1 AND historico_escolar_1.ano = (( SELECT he.ano
                                                                                                                                                                                                                                                                        FROM pmieducar.historico_escolar he
                                                                                                                                                                                                                                                                        WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                                                                                                                                                 FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                                                                                                                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 2::text
                                                                                                                                                                                                                                                                        LIMIT 1))
         LIMIT 1) AS nota_2serie,
       ( SELECT
             CASE
                 WHEN "substring"(btrim(hd.nota::text), 1, 1) <> 0::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 1::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 2::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 3::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 4::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 5::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 6::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 7::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 8::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 9::text THEN replace(hd.nota::text, '.'::text, ','::text)
                 WHEN to_number(btrim(hd.nota::text), '999'::text) > 10::numeric AND to_number(btrim(hd.nota::text), '999'::text) <= 20::numeric THEN replace(btrim(hd.nota::text), '.'::text, ','::text)
                 ELSE replace("substring"(btrim(hd.nota::text), 1, 4), '.'::text, ','::text)
                 END AS replace
         FROM pmieducar.historico_disciplinas hd
                  JOIN pmieducar.historico_escolar historico_escolar_1 ON historico_escolar_1.ref_cod_aluno = hd.ref_ref_cod_aluno AND historico_escolar_1.sequencial = hd.ref_sequencial
         WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND fcn_upper(hd.nm_disciplina::text) = fcn_upper(hd.nm_disciplina::text) AND historico_escolar_1.ativo = 1 AND historico_escolar_1.extra_curricular = 1 AND historico_escolar_1.ano = (( SELECT he.ano
                                                                                                                                                                                                                                                                        FROM pmieducar.historico_escolar he
                                                                                                                                                                                                                                                                        WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                                                                                                                                                 FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                                                                                                                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 3::text
                                                                                                                                                                                                                                                                        LIMIT 1))
         LIMIT 1) AS nota_3serie,
       ( SELECT
             CASE
                 WHEN "substring"(btrim(hd.nota::text), 1, 1) <> 0::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 1::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 2::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 3::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 4::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 5::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 6::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 7::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 8::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 9::text THEN replace(hd.nota::text, '.'::text, ','::text)
                 WHEN to_number(btrim(hd.nota::text), '999'::text) > 10::numeric AND to_number(btrim(hd.nota::text), '999'::text) <= 20::numeric THEN replace(btrim(hd.nota::text), '.'::text, ','::text)
                 ELSE replace("substring"(btrim(hd.nota::text), 1, 4), '.'::text, ','::text)
                 END AS replace
         FROM pmieducar.historico_disciplinas hd
                  JOIN pmieducar.historico_escolar historico_escolar_1 ON historico_escolar_1.ref_cod_aluno = hd.ref_ref_cod_aluno AND historico_escolar_1.sequencial = hd.ref_sequencial
         WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND fcn_upper(hd.nm_disciplina::text) = fcn_upper(hd.nm_disciplina::text) AND historico_escolar_1.ativo = 1 AND historico_escolar_1.extra_curricular = 1 AND historico_escolar_1.ano = (( SELECT he.ano
                                                                                                                                                                                                                                                                        FROM pmieducar.historico_escolar he
                                                                                                                                                                                                                                                                        WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                                                                                                                                                 FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                                                                                                                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 4::text
                                                                                                                                                                                                                                                                        LIMIT 1))
         LIMIT 1) AS nota_4serie,
       ( SELECT
             CASE
                 WHEN "substring"(btrim(hd.nota::text), 1, 1) <> 0::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 1::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 2::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 3::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 4::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 5::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 6::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 7::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 8::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 9::text THEN replace(hd.nota::text, '.'::text, ','::text)
                 WHEN to_number(btrim(hd.nota::text), '999'::text) > 10::numeric AND to_number(btrim(hd.nota::text), '999'::text) <= 20::numeric THEN replace(btrim(hd.nota::text), '.'::text, ','::text)
                 ELSE replace("substring"(btrim(hd.nota::text), 1, 4), '.'::text, ','::text)
                 END AS replace
         FROM pmieducar.historico_disciplinas hd
                  JOIN pmieducar.historico_escolar historico_escolar_1 ON historico_escolar_1.ref_cod_aluno = hd.ref_ref_cod_aluno AND historico_escolar_1.sequencial = hd.ref_sequencial
         WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND fcn_upper(hd.nm_disciplina::text) = fcn_upper(hd.nm_disciplina::text) AND historico_escolar_1.ativo = 1 AND historico_escolar_1.extra_curricular = 1 AND historico_escolar_1.ano = (( SELECT he.ano
                                                                                                                                                                                                                                                                        FROM pmieducar.historico_escolar he
                                                                                                                                                                                                                                                                        WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                                                                                                                                                 FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                                                                                                                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 5::text
                                                                                                                                                                                                                                                                        LIMIT 1))
         LIMIT 1) AS nota_5serie,
       ( SELECT
             CASE
                 WHEN "substring"(btrim(hd.nota::text), 1, 1) <> 0::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 1::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 2::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 3::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 4::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 5::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 6::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 7::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 8::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 9::text THEN replace(hd.nota::text, '.'::text, ','::text)
                 WHEN to_number(btrim(hd.nota::text), '999'::text) > 10::numeric AND to_number(btrim(hd.nota::text), '999'::text) <= 20::numeric THEN replace(btrim(hd.nota::text), '.'::text, ','::text)
                 ELSE replace("substring"(btrim(hd.nota::text), 1, 4), '.'::text, ','::text)
                 END AS replace
         FROM pmieducar.historico_disciplinas hd
                  JOIN pmieducar.historico_escolar historico_escolar_1 ON historico_escolar_1.ref_cod_aluno = hd.ref_ref_cod_aluno AND historico_escolar_1.sequencial = hd.ref_sequencial
         WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND fcn_upper(hd.nm_disciplina::text) = fcn_upper(hd.nm_disciplina::text) AND historico_escolar_1.ativo = 1 AND historico_escolar_1.extra_curricular = 1 AND historico_escolar_1.ano = (( SELECT he.ano
                                                                                                                                                                                                                                                                        FROM pmieducar.historico_escolar he
                                                                                                                                                                                                                                                                        WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                                                                                                                                                 FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                                                                                                                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 6::text
                                                                                                                                                                                                                                                                        LIMIT 1))
         LIMIT 1) AS nota_6serie,
       ( SELECT
             CASE
                 WHEN "substring"(btrim(hd.nota::text), 1, 1) <> 0::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 1::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 2::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 3::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 4::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 5::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 6::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 7::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 8::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 9::text THEN replace(hd.nota::text, '.'::text, ','::text)
                 WHEN to_number(btrim(hd.nota::text), '999'::text) > 10::numeric AND to_number(btrim(hd.nota::text), '999'::text) <= 20::numeric THEN replace(btrim(hd.nota::text), '.'::text, ','::text)
                 ELSE replace("substring"(btrim(hd.nota::text), 1, 4), '.'::text, ','::text)
                 END AS replace
         FROM pmieducar.historico_disciplinas hd
                  JOIN pmieducar.historico_escolar historico_escolar_1 ON historico_escolar_1.ref_cod_aluno = hd.ref_ref_cod_aluno AND historico_escolar_1.sequencial = hd.ref_sequencial
         WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND fcn_upper(hd.nm_disciplina::text) = fcn_upper(hd.nm_disciplina::text) AND historico_escolar_1.ativo = 1 AND historico_escolar_1.extra_curricular = 1 AND historico_escolar_1.ano = (( SELECT he.ano
                                                                                                                                                                                                                                                                        FROM pmieducar.historico_escolar he
                                                                                                                                                                                                                                                                        WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                                                                                                                                                 FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                                                                                                                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 7::text
                                                                                                                                                                                                                                                                        LIMIT 1))
         LIMIT 1) AS nota_7serie,
       ( SELECT
             CASE
                 WHEN "substring"(btrim(hd.nota::text), 1, 1) <> 0::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 1::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 2::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 3::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 4::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 5::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 6::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 7::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 8::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 9::text THEN replace(hd.nota::text, '.'::text, ','::text)
                 WHEN to_number(btrim(hd.nota::text), '999'::text) > 10::numeric AND to_number(btrim(hd.nota::text), '999'::text) <= 20::numeric THEN replace(btrim(hd.nota::text), '.'::text, ','::text)
                 ELSE replace("substring"(btrim(hd.nota::text), 1, 4), '.'::text, ','::text)
                 END AS replace
         FROM pmieducar.historico_disciplinas hd
                  JOIN pmieducar.historico_escolar historico_escolar_1 ON historico_escolar_1.ref_cod_aluno = hd.ref_ref_cod_aluno AND historico_escolar_1.sequencial = hd.ref_sequencial
         WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND fcn_upper(hd.nm_disciplina::text) = fcn_upper(hd.nm_disciplina::text) AND historico_escolar_1.ativo = 1 AND historico_escolar_1.extra_curricular = 1 AND historico_escolar_1.ano = (( SELECT he.ano
                                                                                                                                                                                                                                                                        FROM pmieducar.historico_escolar he
                                                                                                                                                                                                                                                                        WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                                                                                                                                                 FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                                                                                                                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 8::text
                                                                                                                                                                                                                                                                        LIMIT 1))
         LIMIT 1) AS nota_8serie,
       ( SELECT
             CASE
                 WHEN "substring"(btrim(hd.nota::text), 1, 1) <> 0::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 1::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 2::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 3::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 4::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 5::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 6::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 7::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 8::text AND "substring"(btrim(hd.nota::text), 1, 1) <> 9::text THEN replace(hd.nota::text, '.'::text, ','::text)
                 WHEN to_number(btrim(hd.nota::text), '999'::text) > 10::numeric AND to_number(btrim(hd.nota::text), '999'::text) <= 20::numeric THEN replace(btrim(hd.nota::text), '.'::text, ','::text)
                 ELSE replace("substring"(btrim(hd.nota::text), 1, 4), '.'::text, ','::text)
                 END AS replace
         FROM pmieducar.historico_disciplinas hd
                  JOIN pmieducar.historico_escolar historico_escolar_1 ON historico_escolar_1.ref_cod_aluno = hd.ref_ref_cod_aluno AND historico_escolar_1.sequencial = hd.ref_sequencial
         WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND fcn_upper(hd.nm_disciplina::text) = fcn_upper(hd.nm_disciplina::text) AND historico_escolar_1.ativo = 1 AND historico_escolar_1.extra_curricular = 1 AND historico_escolar_1.ano = (( SELECT he.ano
                                                                                                                                                                                                                                                                        FROM pmieducar.historico_escolar he
                                                                                                                                                                                                                                                                        WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                                                                                                                                                 FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                                                                                                                                                 WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 9::text
                                                                                                                                                                                                                                                                        LIMIT 1))
         LIMIT 1) AS nota_9serie,
       ( SELECT he.ano
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                  FROM pmieducar.historico_escolar hee
                                                                                                                  WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 1::text
         LIMIT 1) AS ano_1serie,
       ( SELECT he.ano
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                  FROM pmieducar.historico_escolar hee
                                                                                                                  WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 2::text
         LIMIT 1) AS ano_2serie,
       ( SELECT he.ano
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                  FROM pmieducar.historico_escolar hee
                                                                                                                  WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 3::text
         LIMIT 1) AS ano_3serie,
       ( SELECT he.ano
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                  FROM pmieducar.historico_escolar hee
                                                                                                                  WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 4::text
         LIMIT 1) AS ano_4serie,
       ( SELECT he.ano
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                  FROM pmieducar.historico_escolar hee
                                                                                                                  WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 5::text
         LIMIT 1) AS ano_5serie,
       ( SELECT he.ano
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                  FROM pmieducar.historico_escolar hee
                                                                                                                  WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 6::text
         LIMIT 1) AS ano_6serie,
       ( SELECT he.ano
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                  FROM pmieducar.historico_escolar hee
                                                                                                                  WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 7::text
         LIMIT 1) AS ano_7serie,
       ( SELECT he.ano
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                  FROM pmieducar.historico_escolar hee
                                                                                                                  WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 8::text
         LIMIT 1) AS ano_8serie,
       ( SELECT he.ano
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                  FROM pmieducar.historico_escolar hee
                                                                                                                  WHERE hee.ref_cod_aluno = he.ref_cod_aluno AND "substring"(he.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he.nm_serie::text, 1, 1) = 9::text
         LIMIT 1) AS ano_9serie,
       ( SELECT DISTINCT he.aprovado = 4
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 1::text
                                                                                                                                       LIMIT 1))
         ORDER BY (he.aprovado = 4)
         LIMIT 1) AS transferido1,
       ( SELECT DISTINCT he.aprovado = 4
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 2::text
                                                                                                                                       LIMIT 1))
         ORDER BY (he.aprovado = 4)
         LIMIT 1) AS transferido2,
       ( SELECT DISTINCT he.aprovado = 4
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 3::text
                                                                                                                                       LIMIT 1))
         ORDER BY (he.aprovado = 4)
         LIMIT 1) AS transferido3,
       ( SELECT DISTINCT he.aprovado = 4
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 4::text
                                                                                                                                       LIMIT 1))
         ORDER BY (he.aprovado = 4)
         LIMIT 1) AS transferido4,
       ( SELECT DISTINCT he.aprovado = 4
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 5::text
                                                                                                                                       LIMIT 1))
         ORDER BY (he.aprovado = 4)
         LIMIT 1) AS transferido5,
       ( SELECT DISTINCT he.aprovado = 4
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 6::text
                                                                                                                                       LIMIT 1))
         ORDER BY (he.aprovado = 4)
         LIMIT 1) AS transferido6,
       ( SELECT DISTINCT he.aprovado = 4
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 7::text
                                                                                                                                       LIMIT 1))
         ORDER BY (he.aprovado = 4)
         LIMIT 1) AS transferido7,
       ( SELECT DISTINCT he.aprovado = 4
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 8::text
                                                                                                                                       LIMIT 1))
         ORDER BY (he.aprovado = 4)
         LIMIT 1) AS transferido8,
       ( SELECT DISTINCT he.aprovado = 4
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 9::text
                                                                                                                                       LIMIT 1))
         ORDER BY (he.aprovado = 4)
         LIMIT 1) AS transferido9,
       ( SELECT he.carga_horaria
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 1::text
                                                                                                                                       LIMIT 1))
         LIMIT 1) AS carga_horaria1,
       ( SELECT he.carga_horaria
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 2::text
                                                                                                                                       LIMIT 1))
         LIMIT 1) AS carga_horaria2,
       ( SELECT he.carga_horaria
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 3::text
                                                                                                                                       LIMIT 1))
         LIMIT 1) AS carga_horaria3,
       ( SELECT he.carga_horaria
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 4::text
                                                                                                                                       LIMIT 1))
         LIMIT 1) AS carga_horaria4,
       ( SELECT he.carga_horaria
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 5::text
                                                                                                                                       LIMIT 1))
         LIMIT 1) AS carga_horaria5,
       ( SELECT he.carga_horaria
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 6::text
                                                                                                                                       LIMIT 1))
         LIMIT 1) AS carga_horaria6,
       ( SELECT he.carga_horaria
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 7::text
                                                                                                                                       LIMIT 1))
         LIMIT 1) AS carga_horaria7,
       ( SELECT he.carga_horaria
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 8::text
                                                                                                                                       LIMIT 1))
         LIMIT 1) AS carga_horaria8,
       ( SELECT he.carga_horaria
         FROM pmieducar.historico_escolar he
         WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he.ativo = 1 AND he.extra_curricular = 1 AND he.ano = (( SELECT he_1.ano
                                                                                                                                       FROM pmieducar.historico_escolar he_1
                                                                                                                                       WHERE he_1.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND he_1.ativo = 1 AND he_1.sequencial = (( SELECT max(hee.sequencial) AS max
                                                                                                                                                                                                                                                      FROM pmieducar.historico_escolar hee
                                                                                                                                                                                                                                                      WHERE hee.ref_cod_aluno = he_1.ref_cod_aluno AND "substring"(he_1.nm_serie::text, 1, 1) = "substring"(hee.nm_serie::text, 1, 1) AND hee.ativo = 1 AND hee.extra_curricular = 0)) AND "substring"(he_1.nm_serie::text, 1, 1) = 9::text
                                                                                                                                       LIMIT 1))
         LIMIT 1) AS carga_horaria9
FROM pmieducar.historico_disciplinas
         JOIN pmieducar.historico_escolar ON historico_escolar.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno AND historico_escolar.sequencial = historico_disciplinas.ref_sequencial
WHERE historico_escolar.extra_curricular = 1 AND historico_escolar.ativo = 1
GROUP BY (fcn_upper(historico_disciplinas.nm_disciplina::text)), historico_disciplinas.ref_ref_cod_aluno
ORDER BY (fcn_upper(historico_disciplinas.nm_disciplina::text));
