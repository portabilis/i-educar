 	-- //

 	--
 	-- Cria menu para o relatório usuários e acessos resumido
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$


  insert into portal.menu_submenu values(999244,55,2,'Usuários e acessos resumido','module/Reports/UsuarioAcessoResumido',NULL,3);
  insert into pmicontrolesis.menu values(999244,999244,999300,'Usuários e acessos resumido',11,'module/Reports/UsuarioAcessoResumido','_self',1,15,192, 1);

	-- //@UNDO

  DELETE FROM menu_tipo_usuario WHERE ref_cod_menu_submenu = 999244
  DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999244;
  DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999244;


	-- //
