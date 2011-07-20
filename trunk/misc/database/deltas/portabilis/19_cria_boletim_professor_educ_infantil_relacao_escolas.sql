 	-- //
  
 	--
 	-- Cria os relatórios: Boletim do Professor - Educação Infantil e Relação de Escolas e ordena os menus.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
  
  
  insert into portal.menu_submenu values(999206,55,2,'Boletim do Professor - Educação Infantil','portabilis_boletim_professor_educ_infantil.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999206);
  insert into pmicontrolesis.menu values(999206,999206,999450,'Boletim do Professor - Educação Infantil',5,'portabilis_boletim_professor_educ_infantil.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999206,1,0,1);
  
  insert into portal.menu_submenu values(999605,55,2,'Relação de Escolas','portabilis_relacao_escolas.php',NULL,3);   
  insert into portal.menu_funcionario values(1,0,0,999605);
  insert into pmicontrolesis.menu values(999605,999605,999300,'Relação de Escolas',3,'portabilis_relacao_escolas.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999605,1,0,1);
  
  update pmicontrolesis.menu set ord_menu = 1 where cod_menu = 999202;
  update pmicontrolesis.menu set ord_menu = 3 where cod_menu = 999224;
  update pmicontrolesis.menu set ord_menu = 4 where cod_menu = 999219;
  update pmicontrolesis.menu set ord_menu = 5 where cod_menu = 999205;
  update pmicontrolesis.menu set ord_menu = 6 where cod_menu = 999206;
  update pmicontrolesis.menu set ord_menu = 7 where cod_menu = 999221;
  update pmicontrolesis.menu set ord_menu = 8 where cod_menu = 999218;
  
	-- //@UNDO
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999206;
  delete from pmicontrolesis.menu where cod_menu = 999206;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999206;
  delete from portal.menu_submenu where cod_menu_submenu = 999206;
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999605;
  delete from pmicontrolesis.menu where cod_menu = 999605;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999605;
  delete from portal.menu_submenu where cod_menu_submenu = 999605;
  
	-- //    