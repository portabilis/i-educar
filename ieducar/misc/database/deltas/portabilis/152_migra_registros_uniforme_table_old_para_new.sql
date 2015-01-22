-- 
-- @author   Isac Borgert <isac@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
INSERT INTO distribuicao_uniforme (
		ref_cod_aluno,
        camiseta_curta_qtd,
        camiseta_curta_tm,
        agasalho_qtd,
        agasalho_tm,
        bermudas_tectels_qtd,
        bermudas_tectels_tm,
        bermudas_coton_qtd,
        bermudas_coton_tm,
        tenis_qtd,
        tenis_tm,
        meias_qtd,
        meias_tm,
        ano)
SELECT 
		   ref_cod_aluno,
	       quantidade_camiseta,
	       tamanho_camiseta,
	       quantidade_blusa_jaqueta,
	       tamanho_blusa_jaqueta,
	       quantidade_bermuda,
	       tamanho_bermuda,
	       quantidade_saia,
	       tamanho_saia,
	       quantidade_calcado,
	       tamanho_calcado,
	       quantidade_meia,
	       tamanho_meia,
	       2014
		FROM modules.uniforme_aluno
		WHERE ref_cod_aluno NOT IN
    		(SELECT ref_cod_aluno
     		FROM distribuicao_uniforme 
     		WHERE distribuicao_uniforme.ref_cod_aluno = uniforme_aluno.ref_cod_aluno 
     			AND distribuicao_uniforme.ano = 2014);
