 	-- //

 	--
 	-- Na tabela curso escolar, adiciona campo multi_seriado
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE pmieducar.curso ADD COLUMN multi_seriado integer;


	-- //@UNDO

  ALTER TABLE pmieducar.curso DROP COLUMN multi_seriado;

	-- //
