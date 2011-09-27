 	-- //
  
 	--
 	-- Cria os relatórios: Resultado Final e Mapa do Conselho de Classe.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999608,55,2,'Resultado Final','portabilis_resultado_final.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999608);
  insert into pmicontrolesis.menu values(999608,999608,999450,'Resultado Final',10,'portabilis_resultado_final.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999608,1,0,1); 

  insert into portal.menu_submenu values(999609,55,2,'Mapa do Conselho de Classe','portabilis_mapa_conselho_classe.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999609);
  insert into pmicontrolesis.menu values(999609,999609,999450,'Mapa do Conselho de Classe',9,'portabilis_mapa_conselho_classe.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999609,1,0,1); 
 
	-- //@UNDO
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999608;
  delete from pmicontrolesis.menu where cod_menu = 999608;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999608;
  delete from portal.menu_submenu where cod_menu_submenu = 999608;
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999609;
  delete from pmicontrolesis.menu where cod_menu = 999609;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999609;
  delete from portal.menu_submenu where cod_menu_submenu = 999609;
  
	-- //    