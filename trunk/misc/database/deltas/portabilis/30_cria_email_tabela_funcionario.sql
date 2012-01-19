 	-- //
  
 	--
 	-- Adiciona campo email, na tabela funcionário para ser utilizado na recuperação de senha. 
	-- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  

  ALTER TABLE portal.funcionario ADD COLUMN email character varying(50);
  ALTER TABLE portal.funcionario ADD COLUMN status_token character varying(50);

	-- //@UNDO

  ALTER TABLE portal.funcionario DROP COLUMN email;
  ALTER TABLE portal.funcionario DROP COLUMN status_token;

	-- //
