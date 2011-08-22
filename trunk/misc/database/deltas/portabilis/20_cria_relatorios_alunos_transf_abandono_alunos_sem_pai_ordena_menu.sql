 	-- //
  
 	--
 	-- Cria os relatórios: Alunos Transferidos/Abandono, Alunos sem Pai e ordena menus.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
  
  
  insert into portal.menu_submenu values(999606,55,2,'Relação de Alunos Sem Pai','portabilis_relacao_alunos_sem_pai.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999606);
  insert into pmicontrolesis.menu values(999606,999606,999300,'Relação de Alunos Sem Pai',6,'portabilis_relacao_alunos_sem_pai.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999606,1,0,1);  
    
  insert into portal.menu_submenu values(999607,55,2,'Relação de Alunos Transferidos/Abandono','portabilis_alunos_transferidos_abandono.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999607);
  insert into pmicontrolesis.menu values(999607,999607,999301,'Relação de Alunos Transferidos/Abandono',5,'portabilis_alunos_transferidos_abandono.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999607,1,0,1);

  update pmicontrolesis.menu set ord_menu = 1 where cod_menu = 999105;
  update pmicontrolesis.menu set ord_menu = 2 where cod_menu = 999108;
  update pmicontrolesis.menu set ord_menu = 3 where cod_menu = 999203;
  update pmicontrolesis.menu set ord_menu = 4 where cod_menu = 999204;
  update pmicontrolesis.menu set ord_menu = 5 where cod_menu = 999109;
  update pmicontrolesis.menu set ord_menu = 6 where cod_menu = 999101;
  update pmicontrolesis.menu set ord_menu = 7 where cod_menu = 999606;
  
	-- //@UNDO
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999606;
  delete from pmicontrolesis.menu where cod_menu = 999606;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999606;
  delete from portal.menu_submenu where cod_menu_submenu = 999606;
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999607;
  delete from pmicontrolesis.menu where cod_menu = 999607;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999607;
  delete from portal.menu_submenu where cod_menu_submenu = 999607;
  
	-- //    