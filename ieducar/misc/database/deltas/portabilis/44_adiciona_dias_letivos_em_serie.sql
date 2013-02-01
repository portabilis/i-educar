 	-- //

 	--
 	-- Adiciona dias_letivos em pmieducar.serie
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE pmieducar.serie ADD COLUMN dias_letivos integer;
  UPDATE pmieducar.serie set dias_letivos = 200 where dias_letivos is null;

	-- //@UNDO

    ALTER TABLE pmieducar.serie DROP COLUMN dias_letivos;

	-- //
