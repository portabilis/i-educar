  -- Função para pegar a quantidade de alunos por situação
  --
  -- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  -- Function: relatorio.get_qtde_alunos_situacao(integer, integer)

  -- DROP FUNCTION relatorio.get_qtde_alunos_situacao(integer, integer);

CREATE OR REPLACE FUNCTION relatorio.get_qtde_alunos_situacao(integer, integer)
	RETURNS integer AS
		$BODY$
			SELECT COUNT(*)::integer AS situacao                
			  FROM relatorio.view_situacao                      
		 	 WHERE relatorio.view_situacao.cod_turma = $1 AND   
		       	   relatorio.view_situacao.cod_situacao = $2;   
		$BODY$
	LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_qtde_alunos_situacao(integer, integer)
OWNER TO ieducar;   