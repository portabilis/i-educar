 	-- //

 	--
 	-- Adiciona campo cdd e estante, na tabela acervo.
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE pmieducar.acervo ADD COLUMN cdd character varying(20);
  ALTER TABLE pmieducar.acervo ADD COLUMN estante character varying(20);

	-- //@UNDO

  ALTER TABLE pmieducar.acervo DROP COLUMN cdd;
  ALTER TABLE pmieducar.acervo DROP COLUMN estante;

	-- //
