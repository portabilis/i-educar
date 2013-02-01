 	-- //

 	--
 	-- Adiciona bloquear_cadastro_turma_para_serie_com_vagas em pmieducar.escola_serie
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE pmieducar.escola_serie ADD COLUMN bloquear_cadastro_turma_para_serie_com_vagas integer;


	-- //@UNDO

    ALTER TABLE pmieducar.escola_serie DROP COLUMN bloquear_cadastro_turma_para_serie_com_vagas;

	-- //
