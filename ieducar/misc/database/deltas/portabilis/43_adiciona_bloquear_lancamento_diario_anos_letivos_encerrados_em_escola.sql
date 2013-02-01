 	-- //

 	--
 	-- Adiciona bloquear_lancamento_diario_anos_letivos_encerrados em pmieducar.escola
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE pmieducar.escola ADD COLUMN bloquear_lancamento_diario_anos_letivos_encerrados integer;


	-- //@UNDO

    ALTER TABLE pmieducar.escola DROP COLUMN bloquear_lancamento_diario_anos_letivos_encerrados;

	-- //
