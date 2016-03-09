  -- Função para pegar a quantidade de alunos por situação
  --
  -- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

-- Function: relatorio.get_qtde_alunos_situacao(integer, integer, character, integer, integer, integer)

-- DROP FUNCTION relatorio.get_qtde_alunos_situacao(integer, integer, character, integer, integer, integer);

CREATE OR REPLACE FUNCTION relatorio.get_qtde_alunos_situacao(integer, integer, character, integer, integer, integer) 
RETURNS integer AS
	$BODY$
		SELECT COUNT(*)::integer AS situacao                
		  FROM relatorio.view_situacao
    INNER JOIN pmieducar.matricula 	    ON (relatorio.view_situacao.cod_matricula = pmieducar.matricula.cod_matricula)
	INNER JOIN pmieducar.aluno     	    ON (pmieducar.matricula.ref_cod_aluno = pmieducar.aluno.cod_aluno)
	INNER JOIN cadastro.fisica     	    ON (pmieducar.aluno.ref_idpes = cadastro.fisica.idpes)
	INNER JOIN cadastro.pessoa     	    ON (cadastro.fisica.idpes = cadastro.pessoa.idpes)
	RIGHT JOIN cadastro.endereco_pessoa ON (cadastro.pessoa.idpes = cadastro.endereco_pessoa.idpes)
	RIGHT JOIN public.bairro	        ON (cadastro.endereco_pessoa.idbai = public.bairro.idbai)
         WHERE (CASE WHEN $3 = 'M' THEN cadastro.fisica.sexo = 'M'
		             WHEN $3 = 'F' THEN cadastro.fisica.sexo = 'F'
		             WHEN $3 = 'A' THEN cadastro.fisica.sexo IN ('M', 'F')
	            END) AND
	           ((($4 > 0
			   AND (SELECT substring(age(CURRENT_DATE, fisica.data_nasc),1,2)
				      FROM cadastro.pessoa,
				           cadastro.fisica
				     WHERE aluno.ref_idpes = fisica.idpes AND
				           fisica.idpes = pessoa.idpes)::integer >= $4) AND
				           ($5 > 0 AND
				           (SELECT substring(age(CURRENT_DATE, fisica.data_nasc),1,2)
					          FROM cadastro.pessoa,
					        	   cadastro.fisica
					 		 WHERE aluno.ref_idpes = fisica.idpes AND
					       		   fisica.idpes = pessoa.idpes)::integer <= $5)) OR
					       ($4 = 0)  AND
					       ($5 = 0)) AND
		       (CASE WHEN $6 > 0 THEN public.bairro.idbai = $6 END) AND 
		       relatorio.view_situacao.cod_turma = $1 AND   
		       relatorio.view_situacao.cod_situacao = $2;   
	$BODY$
LANGUAGE sql VOLATILE;
ALTER FUNCTION relatorio.get_qtde_alunos_situacao(integer, integer, character, integer, integer, integer)
OWNER TO ieducar;