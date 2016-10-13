-- Corrige function que trás carga horária por componente
-- Adiciona mais colunas na view de historico escolar 9 anos
-- @author Caroline Salib <caroline@portabilis.com.br>

DROP VIEW relatorio.view_historico_9anos;


DROP FUNCTION relatorio.historico_carga_horaria_componente(character varying, character varying, integer);


CREATE OR REPLACE FUNCTION relatorio.historico_carga_horaria_componente(nm_componente character varying, nm_serie character varying, escola_id integer) RETURNS numeric AS $BODY$ BEGIN RETURN
  (SELECT to_number(ccae.carga_horaria,'999')
   FROM modules.componente_curricular_ano_escolar ccae
   INNER JOIN modules.componente_curricular cc ON (fcn_upper(relatorio.get_texto_sem_caracter_especial(cc.nome)) = fcn_upper(relatorio.get_texto_sem_caracter_especial(nm_componente))
                                                   AND cc.id = ccae.componente_curricular_id)
   INNER JOIN pmieducar.serie s ON (fcn_upper(relatorio.get_texto_sem_caracter_especial(s.nm_serie)) = fcn_upper(relatorio.get_texto_sem_caracter_especial(nm_serie))
                                    AND s.cod_serie = ccae.ano_escolar_id)
   LEFT JOIN pmieducar.escola_serie es ON (es.ref_cod_escola = escola_id
                                           AND es.ref_cod_serie = s.cod_serie)
   ORDER BY ref_cod_escola LIMIT 1); END; $BODY$ LANGUAGE plpgsql VOLATILE;


ALTER FUNCTION relatorio.historico_carga_horaria_componente(character varying, character varying, integer) OWNER TO ieducar;


CREATE OR REPLACE VIEW relatorio.view_historico_9anos AS
SELECT ref_ref_cod_aluno AS cod_aluno,
       nm_disciplina AS disciplina,

  (SELECT CASE
              WHEN ((substring(trim(hd.nota),1,1) <> 0)
                    AND (substring(trim(hd.nota),1,1) <> 1)
                    AND (substring(trim(hd.nota),1,1) <> 2)
                    AND (substring(trim(hd.nota),1,1) <> 3)
                    AND (substring(trim(hd.nota),1,1) <> 4)
                    AND (substring(trim(hd.nota),1,1) <> 5)
                    AND (substring(trim(hd.nota),1,1) <> 6)
                    AND (substring(trim(hd.nota),1,1) <> 7)
                    AND (substring(trim(hd.nota),1,1) <> 8)
                    AND (substring(trim(hd.nota),1,1) <> 9)) THEN replace(hd.nota,'.',',')
              WHEN ((to_number(trim(hd.nota), '999') > 10)
                    AND (to_number(trim(hd.nota), '999') <= 20)) THEN replace(trim(hd.nota),'.',',')
              ELSE replace(substring(trim(hd.nota),1,4),'.',',')
          END
   FROM pmieducar.historico_disciplinas hd
   INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = hd.ref_ref_cod_aluno
                                              AND historico_escolar.sequencial = hd.ref_sequencial)
   WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND hd.nm_disciplina = historico_disciplinas.nm_disciplina
     AND historico_escolar.ativo = 1
     AND historico_escolar.extra_curricular = 0
     AND substring(historico_escolar.nm_serie,1,1) = 1 LIMIT 1) AS nota_1serie,

  (SELECT CASE
              WHEN ((substring(trim(hd.nota),1,1) <> 0)
                    AND (substring(trim(hd.nota),1,1) <> 1)
                    AND (substring(trim(hd.nota),1,1) <> 2)
                    AND (substring(trim(hd.nota),1,1) <> 3)
                    AND (substring(trim(hd.nota),1,1) <> 4)
                    AND (substring(trim(hd.nota),1,1) <> 5)
                    AND (substring(trim(hd.nota),1,1) <> 6)
                    AND (substring(trim(hd.nota),1,1) <> 7)
                    AND (substring(trim(hd.nota),1,1) <> 8)
                    AND (substring(trim(hd.nota),1,1) <> 9)) THEN replace(hd.nota,'.',',')
              WHEN ((to_number(trim(hd.nota), '999') > 10)
                    AND (to_number(trim(hd.nota), '999') <= 20)) THEN replace(trim(hd.nota),'.',',')
              ELSE replace(substring(trim(hd.nota),1,4),'.',',')
          END
   FROM pmieducar.historico_disciplinas hd
   INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = hd.ref_ref_cod_aluno
                                              AND historico_escolar.sequencial = hd.ref_sequencial)
   WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND hd.nm_disciplina = historico_disciplinas.nm_disciplina
     AND historico_escolar.ativo = 1
     AND historico_escolar.extra_curricular = 0
     AND substring(historico_escolar.nm_serie,1,1) = 2 LIMIT 1) AS nota_2serie,

  (SELECT CASE
              WHEN ((substring(trim(hd.nota),1,1) <> 0)
                    AND (substring(trim(hd.nota),1,1) <> 1)
                    AND (substring(trim(hd.nota),1,1) <> 2)
                    AND (substring(trim(hd.nota),1,1) <> 3)
                    AND (substring(trim(hd.nota),1,1) <> 4)
                    AND (substring(trim(hd.nota),1,1) <> 5)
                    AND (substring(trim(hd.nota),1,1) <> 6)
                    AND (substring(trim(hd.nota),1,1) <> 7)
                    AND (substring(trim(hd.nota),1,1) <> 8)
                    AND (substring(trim(hd.nota),1,1) <> 9)) THEN replace(hd.nota,'.',',')
              WHEN ((to_number(trim(hd.nota), '999') > 10)
                    AND (to_number(trim(hd.nota), '999') <= 20)) THEN replace(trim(hd.nota),'.',',')
              ELSE replace(substring(trim(hd.nota),1,4),'.',',')
          END
   FROM pmieducar.historico_disciplinas hd
   INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = hd.ref_ref_cod_aluno
                                              AND historico_escolar.sequencial = hd.ref_sequencial)
   WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND hd.nm_disciplina = historico_disciplinas.nm_disciplina
     AND historico_escolar.ativo = 1
     AND historico_escolar.extra_curricular = 0
     AND substring(historico_escolar.nm_serie,1,1) = 3 LIMIT 1) AS nota_3serie,

  (SELECT CASE
              WHEN ((substring(trim(hd.nota),1,1) <> 0)
                    AND (substring(trim(hd.nota),1,1) <> 1)
                    AND (substring(trim(hd.nota),1,1) <> 2)
                    AND (substring(trim(hd.nota),1,1) <> 3)
                    AND (substring(trim(hd.nota),1,1) <> 4)
                    AND (substring(trim(hd.nota),1,1) <> 5)
                    AND (substring(trim(hd.nota),1,1) <> 6)
                    AND (substring(trim(hd.nota),1,1) <> 7)
                    AND (substring(trim(hd.nota),1,1) <> 8)
                    AND (substring(trim(hd.nota),1,1) <> 9)) THEN replace(hd.nota,'.',',')
              WHEN ((to_number(trim(hd.nota), '999') > 10)
                    AND (to_number(trim(hd.nota), '999') <= 20)) THEN replace(trim(hd.nota),'.',',')
              ELSE replace(substring(trim(hd.nota),1,4),'.',',')
          END
   FROM pmieducar.historico_disciplinas hd
   INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = hd.ref_ref_cod_aluno
                                              AND historico_escolar.sequencial = hd.ref_sequencial)
   WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND hd.nm_disciplina = historico_disciplinas.nm_disciplina
     AND historico_escolar.ativo = 1
     AND historico_escolar.extra_curricular = 0
     AND substring(historico_escolar.nm_serie,1,1) = 4 LIMIT 1) AS nota_4serie,

  (SELECT CASE
              WHEN ((substring(trim(hd.nota),1,1) <> 0)
                    AND (substring(trim(hd.nota),1,1) <> 1)
                    AND (substring(trim(hd.nota),1,1) <> 2)
                    AND (substring(trim(hd.nota),1,1) <> 3)
                    AND (substring(trim(hd.nota),1,1) <> 4)
                    AND (substring(trim(hd.nota),1,1) <> 5)
                    AND (substring(trim(hd.nota),1,1) <> 6)
                    AND (substring(trim(hd.nota),1,1) <> 7)
                    AND (substring(trim(hd.nota),1,1) <> 8)
                    AND (substring(trim(hd.nota),1,1) <> 9)) THEN replace(hd.nota,'.',',')
              WHEN ((to_number(trim(hd.nota), '999') > 10)
                    AND (to_number(trim(hd.nota), '999') <= 20)) THEN replace(trim(hd.nota),'.',',')
              ELSE replace(substring(trim(hd.nota),1,4),'.',',')
          END
   FROM pmieducar.historico_disciplinas hd
   INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = hd.ref_ref_cod_aluno
                                              AND historico_escolar.sequencial = hd.ref_sequencial)
   WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND hd.nm_disciplina = historico_disciplinas.nm_disciplina
     AND historico_escolar.ativo = 1
     AND historico_escolar.extra_curricular = 0
     AND substring(historico_escolar.nm_serie,1,1) = 5 LIMIT 1) AS nota_5serie,

  (SELECT CASE
              WHEN ((substring(trim(hd.nota),1,1) <> 0)
                    AND (substring(trim(hd.nota),1,1) <> 1)
                    AND (substring(trim(hd.nota),1,1) <> 2)
                    AND (substring(trim(hd.nota),1,1) <> 3)
                    AND (substring(trim(hd.nota),1,1) <> 4)
                    AND (substring(trim(hd.nota),1,1) <> 5)
                    AND (substring(trim(hd.nota),1,1) <> 6)
                    AND (substring(trim(hd.nota),1,1) <> 7)
                    AND (substring(trim(hd.nota),1,1) <> 8)
                    AND (substring(trim(hd.nota),1,1) <> 9)) THEN replace(hd.nota,'.',',')
              WHEN ((to_number(trim(hd.nota), '999') > 10)
                    AND (to_number(trim(hd.nota), '999') <= 20)) THEN replace(trim(hd.nota),'.',',')
              ELSE replace(substring(trim(hd.nota),1,4),'.',',')
          END
   FROM pmieducar.historico_disciplinas hd
   INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = hd.ref_ref_cod_aluno
                                              AND historico_escolar.sequencial = hd.ref_sequencial)
   WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND hd.nm_disciplina = historico_disciplinas.nm_disciplina
     AND historico_escolar.ativo = 1
     AND historico_escolar.extra_curricular = 0
     AND substring(historico_escolar.nm_serie,1,1) = 6 LIMIT 1) AS nota_6serie,

  (SELECT CASE
              WHEN ((substring(trim(hd.nota),1,1) <> 0)
                    AND (substring(trim(hd.nota),1,1) <> 1)
                    AND (substring(trim(hd.nota),1,1) <> 2)
                    AND (substring(trim(hd.nota),1,1) <> 3)
                    AND (substring(trim(hd.nota),1,1) <> 4)
                    AND (substring(trim(hd.nota),1,1) <> 5)
                    AND (substring(trim(hd.nota),1,1) <> 6)
                    AND (substring(trim(hd.nota),1,1) <> 7)
                    AND (substring(trim(hd.nota),1,1) <> 8)
                    AND (substring(trim(hd.nota),1,1) <> 9)) THEN replace(hd.nota,'.',',')
              WHEN ((to_number(trim(hd.nota), '999') > 10)
                    AND (to_number(trim(hd.nota), '999') <= 20)) THEN replace(trim(hd.nota),'.',',')
              ELSE replace(substring(trim(hd.nota),1,4),'.',',')
          END
   FROM pmieducar.historico_disciplinas hd
   INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = hd.ref_ref_cod_aluno
                                              AND historico_escolar.sequencial = hd.ref_sequencial)
   WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND hd.nm_disciplina = historico_disciplinas.nm_disciplina
     AND historico_escolar.ativo = 1
     AND historico_escolar.extra_curricular = 0
     AND substring(historico_escolar.nm_serie,1,1) = 7 LIMIT 1) AS nota_7serie,

  (SELECT CASE
              WHEN ((substring(trim(hd.nota),1,1) <> 0)
                    AND (substring(trim(hd.nota),1,1) <> 1)
                    AND (substring(trim(hd.nota),1,1) <> 2)
                    AND (substring(trim(hd.nota),1,1) <> 3)
                    AND (substring(trim(hd.nota),1,1) <> 4)
                    AND (substring(trim(hd.nota),1,1) <> 5)
                    AND (substring(trim(hd.nota),1,1) <> 6)
                    AND (substring(trim(hd.nota),1,1) <> 7)
                    AND (substring(trim(hd.nota),1,1) <> 8)
                    AND (substring(trim(hd.nota),1,1) <> 9)) THEN replace(hd.nota,'.',',')
              WHEN ((to_number(trim(hd.nota), '999') > 10)
                    AND (to_number(trim(hd.nota), '999') <= 20)) THEN replace(trim(hd.nota),'.',',')
              ELSE replace(substring(trim(hd.nota),1,4),'.',',')
          END
   FROM pmieducar.historico_disciplinas hd
   INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = hd.ref_ref_cod_aluno
                                              AND historico_escolar.sequencial = hd.ref_sequencial)
   WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND hd.nm_disciplina = historico_disciplinas.nm_disciplina
     AND historico_escolar.ativo = 1
     AND historico_escolar.extra_curricular = 0
     AND substring(historico_escolar.nm_serie,1,1) = 8 LIMIT 1) AS nota_8serie,

  (SELECT CASE
              WHEN ((substring(trim(hd.nota),1,1) <> 0)
                    AND (substring(trim(hd.nota),1,1) <> 1)
                    AND (substring(trim(hd.nota),1,1) <> 2)
                    AND (substring(trim(hd.nota),1,1) <> 3)
                    AND (substring(trim(hd.nota),1,1) <> 4)
                    AND (substring(trim(hd.nota),1,1) <> 5)
                    AND (substring(trim(hd.nota),1,1) <> 6)
                    AND (substring(trim(hd.nota),1,1) <> 7)
                    AND (substring(trim(hd.nota),1,1) <> 8)
                    AND (substring(trim(hd.nota),1,1) <> 9)) THEN replace(hd.nota,'.',',')
              WHEN ((to_number(trim(hd.nota), '999') > 10)
                    AND (to_number(trim(hd.nota), '999') <= 20)) THEN replace(trim(hd.nota),'.',',')
              ELSE replace(substring(trim(hd.nota),1,4),'.',',')
          END
   FROM pmieducar.historico_disciplinas hd
   INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = hd.ref_ref_cod_aluno
                                              AND historico_escolar.sequencial = hd.ref_sequencial)
   WHERE hd.ref_ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND hd.nm_disciplina = historico_disciplinas.nm_disciplina
     AND historico_escolar.ativo = 1
     AND historico_escolar.extra_curricular = 0
     AND substring(historico_escolar.nm_serie,1,1) = 9 LIMIT 1) AS nota_9serie,

  (SELECT ano
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 1 LIMIT 1) AS ano_1serie,

  (SELECT escola
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 1 LIMIT 1) AS escola_1serie,

  (SELECT escola_cidade
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 1 LIMIT 1) AS escola_cidade_1serie,

  (SELECT escola_uf
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 1 LIMIT 1) AS escola_uf_1serie,

  (SELECT ano
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 2 LIMIT 1) AS ano_2serie,

  (SELECT escola
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 2 LIMIT 1) AS escola_2serie,

  (SELECT escola_cidade
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 2 LIMIT 1) AS escola_cidade_2serie,

  (SELECT escola_uf
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 2 LIMIT 1) AS escola_uf_2serie,

  (SELECT ano
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 3 LIMIT 1) AS ano_3serie,

  (SELECT escola
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 3 LIMIT 1) AS escola_3serie,

  (SELECT escola_cidade
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 3 LIMIT 1) AS escola_cidade_3serie,

  (SELECT escola_uf
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 3 LIMIT 1) AS escola_uf_3serie,

  (SELECT ano
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 4 LIMIT 1) AS ano_4serie,

  (SELECT escola
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 4 LIMIT 1) AS escola_4serie,

  (SELECT escola_cidade
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 4 LIMIT 1) AS escola_cidade_4serie,

  (SELECT escola_uf
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 4 LIMIT 1) AS escola_uf_4serie,

  (SELECT ano
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 5 LIMIT 1) AS ano_5serie,

  (SELECT escola
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 5 LIMIT 1) AS escola_5serie,

  (SELECT escola_cidade
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 5 LIMIT 1) AS escola_cidade_5serie,

  (SELECT escola_uf
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 5 LIMIT 1) AS escola_uf_5serie,

  (SELECT ano
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 6 LIMIT 1) AS ano_6serie,

  (SELECT escola
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 6 LIMIT 1) AS escola_6serie,

  (SELECT escola_cidade
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 6 LIMIT 1) AS escola_cidade_6serie,

  (SELECT escola_uf
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 6 LIMIT 1) AS escola_uf_6serie,

  (SELECT ano
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 7 LIMIT 1) AS ano_7serie,

  (SELECT escola
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 7 LIMIT 1) AS escola_7serie,

  (SELECT escola_cidade
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 7 LIMIT 1) AS escola_cidade_7serie,

  (SELECT escola_uf
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 7 LIMIT 1) AS escola_uf_7serie,

  (SELECT ano
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 8 LIMIT 1) AS ano_8serie,

  (SELECT escola
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 8 LIMIT 1) AS escola_8serie,

  (SELECT escola_cidade
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 8 LIMIT 1) AS escola_cidade_8serie,

  (SELECT escola_uf
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 8 LIMIT 1) AS escola_uf_8serie,

  (SELECT ano
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 9 LIMIT 1) AS ano_9serie,

  (SELECT escola
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 9 LIMIT 1) AS escola_9serie,

  (SELECT escola_cidade
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 9 LIMIT 1) AS escola_cidade_9serie,

  (SELECT escola_uf
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 9 LIMIT 1) AS escola_uf_9serie,

  (SELECT DISTINCT he.aprovado = 4
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 1 LIMIT 1) AS transferido1,

  (SELECT DISTINCT he.aprovado = 4
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 2 LIMIT 1) AS transferido2,

  (SELECT DISTINCT he.aprovado = 4
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 3 LIMIT 1) AS transferido3,

  (SELECT DISTINCT he.aprovado = 4
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 4 LIMIT 1) AS transferido4,

  (SELECT DISTINCT he.aprovado = 4
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 5 LIMIT 1) AS transferido5,

  (SELECT DISTINCT he.aprovado = 4
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 6 LIMIT 1) AS transferido6,

  (SELECT DISTINCT he.aprovado = 4
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 11
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 7 LIMIT 1) AS transferido7,

  (SELECT DISTINCT he.aprovado = 4
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 8 LIMIT 1) AS transferido8,

  (SELECT DISTINCT he.aprovado = 4
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 9 LIMIT 1) AS transferido9,

  (SELECT he.carga_horaria
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 1 LIMIT 1) AS carga_horaria1,

  (SELECT he.carga_horaria
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 2 LIMIT 1) AS carga_horaria2,

  (SELECT he.carga_horaria
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 3 LIMIT 1) AS carga_horaria3,

  (SELECT he.carga_horaria
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 4 LIMIT 1) AS carga_horaria4,

  (SELECT he.carga_horaria
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 5 LIMIT 1) AS carga_horaria5,

  (SELECT he.carga_horaria
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 6 LIMIT 1) AS carga_horaria6,

  (SELECT he.carga_horaria
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 7 LIMIT 1) AS carga_horaria7,

  (SELECT he.carga_horaria
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 8 LIMIT 1) AS carga_horaria8,

  (SELECT he.carga_horaria
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 9 LIMIT 1) AS carga_horaria9,

  (SELECT textcat_all(observacao)
   FROM pmieducar.historico_escolar phe
   WHERE phe.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND phe.ativo = 1
     AND phe.extra_curricular = 0
     AND phe.sequencial =
       (SELECT max(he.sequencial)
        FROM pmieducar.historico_escolar he
        WHERE he.ref_cod_instituicao = phe.ref_cod_instituicao
          AND substring(he.nm_serie,1,1) = substring(phe.nm_serie,1,1)
          AND he.ref_cod_aluno = phe.ref_cod_aluno
          AND ativo = 1)) AS observacao_all,

  (SELECT m.cod_matricula
   FROM pmieducar.matricula m
   WHERE m.ano =
       (SELECT ano
        FROM historico_escolar
        WHERE aprovado = 4
          AND ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
          AND ativo = 1
          AND extra_curricular = 0
        ORDER BY ano DESC, sequencial DESC LIMIT 1)
     AND
       (SELECT sequencial
        FROM historico_escolar
        WHERE ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
          AND ativo = 1
          AND extra_curricular = 0
        ORDER BY ano DESC, sequencial DESC LIMIT 1) =
       (SELECT sequencial
        FROM historico_escolar
        WHERE aprovado = 4
          AND ativo = 1
          AND extra_curricular = 0
          AND ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
        ORDER BY ano DESC, sequencial DESC LIMIT 1)
     AND m.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND m.ativo = 1
     AND m.aprovado = 4
   ORDER BY m.cod_matricula DESC LIMIT 1) AS matricula_transferido,

  (SELECT relatorio.historico_carga_horaria_componente(historico_disciplinas.nm_disciplina, he.nm_serie, he.ref_cod_escola)
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 1 LIMIT 1) AS ch_componente_1,

  (SELECT relatorio.historico_carga_horaria_componente(historico_disciplinas.nm_disciplina, he.nm_serie, he.ref_cod_escola)
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 2 LIMIT 1) AS ch_componente_2,

  (SELECT relatorio.historico_carga_horaria_componente(historico_disciplinas.nm_disciplina, he.nm_serie, he.ref_cod_escola)
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 3 LIMIT 1) AS ch_componente_3,

  (SELECT relatorio.historico_carga_horaria_componente(historico_disciplinas.nm_disciplina, he.nm_serie, he.ref_cod_escola)
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 4 LIMIT 1) AS ch_componente_4,

  (SELECT relatorio.historico_carga_horaria_componente(historico_disciplinas.nm_disciplina, he.nm_serie, he.ref_cod_escola)
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 5 LIMIT 1) AS ch_componente_5,

  (SELECT relatorio.historico_carga_horaria_componente(historico_disciplinas.nm_disciplina, he.nm_serie, he.ref_cod_escola)
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 6 LIMIT 1) AS ch_componente_6,

  (SELECT relatorio.historico_carga_horaria_componente(historico_disciplinas.nm_disciplina, he.nm_serie, he.ref_cod_escola)
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 7 LIMIT 1) AS ch_componente_7,

  (SELECT relatorio.historico_carga_horaria_componente(historico_disciplinas.nm_disciplina, he.nm_serie, he.ref_cod_escola)
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 8 LIMIT 1) AS ch_componente_8,

  (SELECT relatorio.historico_carga_horaria_componente(historico_disciplinas.nm_disciplina, he.nm_serie, he.ref_cod_escola)
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 9 LIMIT 1) AS ch_componente_9,

  (SELECT he.frequencia
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 1 LIMIT 1) AS frequencia1,

  (SELECT he.frequencia
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 2 LIMIT 1) AS frequencia2,

  (SELECT he.frequencia
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 3 LIMIT 1) AS frequencia3,

  (SELECT he.frequencia
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 4 LIMIT 1) AS frequencia4,

  (SELECT he.frequencia
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 5 LIMIT 1) AS frequencia5,

  (SELECT he.frequencia
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 6 LIMIT 1) AS frequencia6,

  (SELECT he.frequencia
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 7 LIMIT 1) AS frequencia7,

  (SELECT he.frequencia
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 8 LIMIT 1) AS frequencia8,

  (SELECT he.frequencia
   FROM pmieducar.historico_escolar he
   WHERE he.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
     AND he.ativo = 1
     AND he.sequencial =
       (SELECT max(hee.sequencial)
        FROM pmieducar.historico_escolar hee
        WHERE hee.ref_cod_aluno = he.ref_cod_aluno
          AND substring(he.nm_serie,1,1) = substring(hee.nm_serie,1,1)
          AND hee.ativo = 1
          AND hee.extra_curricular = 0)
     AND substring(he.nm_serie,1,1) = 9 LIMIT 1) AS frequencia9
FROM pmieducar.historico_disciplinas
INNER JOIN pmieducar.historico_escolar ON (historico_escolar.ref_cod_aluno = historico_disciplinas.ref_ref_cod_aluno
                                           AND historico_escolar.sequencial = historico_disciplinas.ref_sequencial)
WHERE historico_escolar.extra_curricular = 0
  AND historico_escolar.ativo = 1
GROUP BY ref_ref_cod_aluno,
         nm_disciplina
ORDER BY nm_disciplina;


ALTER TABLE relatorio.view_historico_9anos OWNER TO ieducar;