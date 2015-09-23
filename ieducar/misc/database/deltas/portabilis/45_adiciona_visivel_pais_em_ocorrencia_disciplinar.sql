 	-- //

 	--
 	-- Adiciona visivel_pais em pmieducar.matricula_ocorrencia_disciplinar
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE pmieducar.matricula_ocorrencia_disciplinar ADD COLUMN visivel_pais integer;


	-- //@UNDO

    ALTER TABLE pmieducar.matricula_ocorrencia_disciplinar DROP COLUMN visivel_pais;

	-- //
