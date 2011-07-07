 	-- //
  
 	--
 	-- Cria relatório Boletim Escolar - Parecer Desc. (Geral) e altera as ordens dos Menus.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
  
  update pmicontrolesis.menu set ord_menu = 2 where cod_menu = 999202;
  update pmicontrolesis.menu set ord_menu = 4 where cod_menu = 999219;
  update pmicontrolesis.menu set ord_menu = 5 where cod_menu = 999218;
  update pmicontrolesis.menu set ord_menu = 6 where cod_menu = 999221;

  insert into portal.menu_submenu values(999224,55,2,'Boletim Escolar - Parecer Desc. (Geral)','portabilis_boletim_parecer_geral.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999224);
  insert into pmicontrolesis.menu values(999224,999224,999450,'Boletim Escolar - Parecer Desc. (Geral)',3,'portabilis_boletim_parecer_geral.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999224,1,0,1);
 
	-- //@UNDO
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999224;  
  delete from pmicontrolesis.menu where cod_menu = 999224;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999224;
  delete from  portal.menu_submenu where cod_menu_submenu = 999224;
	
	-- //
