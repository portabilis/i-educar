 	-- //

 	--
 	-- Adiciona bloquear_enturmacao_sem_vagas em pmieducar.escola_serie
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE pmieducar.escola_serie ADD COLUMN bloquear_enturmacao_sem_vagas integer;


	-- //@UNDO

    ALTER TABLE pmieducar.escola_serie DROP COLUMN bloquear_enturmacao_sem_vagas;

	-- //
