  -- Coloca os ids referentes a cada escola
  -- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

UPDATE pmieducar.historico_escolar
   SET ref_cod_escola = e.cod_escola
  FROM pmieducar.escola AS e,
       cadastro.pessoa,
       cadastro.juridica
 WHERE historico_escolar.escola = (SELECT COALESCE((SELECT COALESCE (fcn_upper(ps.nome),fcn_upper(juridica.fantasia))
						      						  FROM cadastro.pessoa ps, cadastro.juridica
						     						 WHERE escola.ref_idpes = juridica.idpes
						       						   AND juridica.idpes = ps.idpes
						       						   AND ps.idpes = escola.ref_idpes),
						   						   (SELECT nm_escola
						      						  FROM pmieducar.escola_complemento
						     						 WHERE ref_cod_escola = escola.cod_escola)) AS nome_escola
				    				 FROM pmieducar.escola
				   				    WHERE escola.cod_escola = e.cod_escola)
  AND (e.ref_idpes = pessoa.idpes or (e.ref_idpes = juridica.idpes
  AND juridica.idpes = pessoa.idpes))