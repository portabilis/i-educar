-- @author   Caroline Salib <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
-- Cria function para buscar a quantidade de alunos no ano/escola

CREATE OR REPLACE FUNCTION relatorio.get_qtde_alunos(integer, integer) RETURNS bigint AS $BODY$
SELECT COUNT(*)
FROM pmieducar.matricula
WHERE matricula.ativo = 1
  AND (CASE WHEN 0 = $1 THEN TRUE ELSE matricula.ano = $1 END)
  AND (CASE WHEN 0 = $2 THEN TRUE ELSE matricula.ref_ref_cod_escola = $2 END); $BODY$ LANGUAGE SQL VOLATILE;


ALTER FUNCTION relatorio.get_qtde_alunos(integer, integer) OWNER TO ieducar;

