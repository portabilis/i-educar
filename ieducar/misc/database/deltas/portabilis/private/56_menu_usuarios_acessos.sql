 	-- //
  
 	--
 	-- Cria menu para o relatório Usuários e Acessos
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999223,55,2,'Usuários e Acessos','module/Reports/UsuarioAcesso',NULL,3);
  insert into pmicontrolesis.menu values(999223,999223,999300,'Usuários e Acessos',11,'module/Reports/UsuarioAcesso','_self',1,15,192);

	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999223;
  delete from portal.menu_submenu where cod_menu_submenu = 999223;
  
	-- //
