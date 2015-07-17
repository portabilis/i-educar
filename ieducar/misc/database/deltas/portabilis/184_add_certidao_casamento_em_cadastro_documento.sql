 	-- //

 	--
 	-- Na tabela cadastro.documento, adiciona campo certidao_casamento
	-- @author   Alan Felipe Farias <alan@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

  ALTER TABLE cadastro.documento ADD COLUMN certidao_casamento varchar(50);

	-- //@UNDO

  ALTER TABLE cadastro.documento DROP COLUMN certidao_casamento;

