 	-- //

 	--
 	-- Adiciona tipo_boletim em pmieducar.turma
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE pmieducar.turma ADD COLUMN tipo_boletim integer;


	-- //@UNDO

  ALTER TABLE pmieducar.turma DROP COLUMN tipo_boletim;

	-- //
