-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

DROP FUNCTION relatorio.get_disciplina_historico_parauapebas(integer, integer);

CREATE OR REPLACE FUNCTION relatorio.get_disciplina_historico_parauapebas(integer)
  RETURNS character varying AS
$BODY$
	SELECT relatorio.get_texto_sem_caracter_especial(cc.nome) AS disciplina
	  FROM modules.componente_curricular cc 
         WHERE cc.id = $1
$BODY$
  LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_disciplina_historico_parauapebas(integer)
  OWNER TO ieducar;




DROP FUNCTION relatorio.get_nota_historico_parauapebas(integer, integer, integer);

CREATE OR REPLACE FUNCTION relatorio.get_nota_historico_parauapebas(integer, integer, integer)
  RETURNS character varying AS
$BODY$
	SELECT nota
	  FROM pmieducar.historico_disciplinas
	 WHERE ref_ref_cod_aluno = $1
	   AND ref_sequencial = $2
	   AND relatorio.get_texto_sem_caracter_especial(nm_disciplina) = relatorio.get_texto_sem_caracter_especial(relatorio.get_disciplina_historico_parauapebas($3))
$BODY$
  LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_nota_historico_parauapebas(integer, integer, integer)
  OWNER TO ieducar;




DROP FUNCTION relatorio.get_ch_historico_parauapebas(integer, integer, integer);

CREATE OR REPLACE FUNCTION relatorio.get_ch_historico_parauapebas(integer, integer, integer)
  RETURNS integer AS
$BODY$

SELECT ccae.carga_horaria::integer
FROM pmieducar.historico_escolar he
INNER JOIN modules.componente_curricular cc ON (UPPER(cc.nome) = UPPER(relatorio.get_disciplina_historico_parauapebas($3)))
INNER JOIN modules.componente_curricular_ano_escolar ccae ON (ccae.componente_curricular_id = cc.id)
WHERE he.ref_cod_aluno = $1
  AND he.sequencial = $2
  AND ccae.ano_escolar_id =
    (SELECT s.cod_serie
     FROM pmieducar.serie s
     WHERE s.ativo = 1
       AND relatorio.get_texto_sem_espaco(s.nm_serie) = relatorio.get_texto_sem_espaco(he.nm_serie)
       AND s.ref_cod_curso =
         (SELECT c.cod_curso
          FROM pmieducar.curso c
          WHERE c.ativo = 1
            AND relatorio.get_texto_sem_espaco(c.nm_curso) = relatorio.get_texto_sem_espaco(he.nm_curso) LIMIT 1) LIMIT 1) LIMIT 1; 
            $BODY$
  LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_ch_historico_parauapebas(integer, integer, integer)
  OWNER TO ieducar;





DROP FUNCTION relatorio.get_ch_historico_parauapebas(integer, integer, integer);

CREATE OR REPLACE FUNCTION relatorio.get_ch_historico_parauapebas(integer, integer, integer)
  RETURNS integer AS
$BODY$

SELECT ccae.carga_horaria::integer
FROM pmieducar.historico_escolar he
INNER JOIN modules.componente_curricular cc ON (UPPER(relatorio.get_texto_sem_caracter_especial(cc.nome)) = UPPER(relatorio.get_disciplina_historico_parauapebas($3)))
INNER JOIN modules.componente_curricular_ano_escolar ccae ON (ccae.componente_curricular_id = cc.id)
WHERE he.ref_cod_aluno = $1
  AND he.sequencial = $2
  AND ccae.ano_escolar_id =
    (SELECT s.cod_serie
     FROM pmieducar.serie s
     WHERE s.ativo = 1
       AND relatorio.get_texto_sem_espaco(s.nm_serie) = relatorio.get_texto_sem_espaco(he.nm_serie)
       AND s.ref_cod_curso =
         (SELECT c.cod_curso
          FROM pmieducar.curso c
          WHERE c.ativo = 1
            AND relatorio.get_texto_sem_espaco(c.nm_curso) = relatorio.get_texto_sem_espaco(he.nm_curso) LIMIT 1) LIMIT 1) LIMIT 1; 
            $BODY$
  LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_ch_historico_parauapebas(integer, integer, integer)
  OWNER TO ieducar;