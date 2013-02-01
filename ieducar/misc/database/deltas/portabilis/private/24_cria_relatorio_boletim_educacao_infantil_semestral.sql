 	-- //
  
 	--
 	-- Cria o relatório: Boletim Escolar - Ed. Infantil Semestral e exclui modelo Boletim Professor - Educação Infantil
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999612,55,2,'Boletim Escolar - Ed. Infantil Semestral','portabilis_boletim_educ_infantil_semestral.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999612);
  insert into pmicontrolesis.menu values(999612,999612,999450,'Boletim Escolar - Ed. Infantil Semestral',2,'portabilis_boletim_educ_infantil_semestral.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999612,1,0,1); 

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999206;
  delete from pmicontrolesis.menu where cod_menu = 999206;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999206;
  delete from portal.menu_submenu where cod_menu_submenu = 999206;

	-- //@UNDO
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999612;
  delete from pmicontrolesis.menu where cod_menu = 999612;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999612;
  delete from portal.menu_submenu where cod_menu_submenu = 999612;
  
	-- //
