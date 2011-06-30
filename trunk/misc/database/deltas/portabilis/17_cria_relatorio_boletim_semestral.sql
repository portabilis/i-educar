 	-- //
  
 	--
 	-- Atualiza sequência de relatório e cria o modelo de relatório Boletim Escolar - Semestral.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
  
  update pmicontrolesis.menu set ord_menu = 5 where cod_menu = 999205;

  insert into portal.menu_submenu values(999221,55,2,'Boletim Escolar - Semestral','portabilis_boletim_semestral.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999221);
  insert into pmicontrolesis.menu values(999221,999221,999450,'Boletim Escolar - Semestral',4,'portabilis_boletim_semestral.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999221,1,0,1);
 
	-- //@UNDO
  
  delete from  portal.menu_submenu where cod_menu_submenu = 999221;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999221;
  delete from pmicontrolesis.menu where cod_menu = 999221;
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999221;
	
	-- //