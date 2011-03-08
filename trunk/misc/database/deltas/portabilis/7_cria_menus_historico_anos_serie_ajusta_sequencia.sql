 	-- //

 	--
 	-- Cria os menus dos novos reatorios registros de avaliacoes.
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
		
 	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu in(999200,999505,999506,999507);	
	delete from pmicontrolesis.menu where cod_menu in(999200,999505,999506,999507);	
    delete from portal.menu_funcionario where ref_cod_menu_submenu in(999200,999505,999506,999507);		
	delete from portal.menu_submenu where cod_menu_submenu in(999200,999505,999506,999507);
	
	insert into portal.menu_submenu values(999505,55,2,'Diário de Classe - Capa (Modelo 1)','portabilis_registro_capa_diario.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999505);
	insert into pmicontrolesis.menu values(999505,999505,999500,'Diário de Classe - Capa (Modelo 1)',6,'portabilis_registro_capa_diario.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999505,1,0,1);

	insert into portal.menu_submenu values(999507,55,2,'Diário de Classe - Capa (Modelo 2)','portabilis_registro_capa_diario_mod2.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999507);
	insert into pmicontrolesis.menu values(999507,999507,999500,'Diário de Classe - Capa (Modelo 2)',7,'portabilis_registro_capa_diario_mod2.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999507,1,0,1);

	insert into portal.menu_submenu values(999506,55,2,'Diário de Classe - Contracapa','portabilis_registro_contra_capa_diario.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999506);
	insert into pmicontrolesis.menu values(999506,999506,999500,'Diário de Classe - Contracapa',8,'portabilis_registro_contra_capa_diario.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999506,1,0,1);
	
	insert into portal.menu_submenu values(999200,55,2,'Histórico Escolar (8 Anos)','portabilis_historico_escolar.php',NULL,3);
    insert into portal.menu_funcionario values(1,0,0,999200);
    insert into pmicontrolesis.menu values(999200,999200,999460,'Histórico Escolar (8 Anos)',1,'portabilis_historico_escolar.php','_self',1,15,122);
    insert into pmieducar.menu_tipo_usuario values(1,999200,1,0,1);

	insert into portal.menu_submenu values(999215,55,2,'Histórico Escolar (9 Anos)','portabilis_historico_escolar_9anos.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999215);
	insert into pmicontrolesis.menu values(999215,999215,999460,'Histórico Escolar (9 Anos)',2,'portabilis_historico_escolar_9anos.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999215,1,0,1);
	
 	-- //@UNDO
	
    delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu in(999200,999215,999505,999506,999507);	
	delete from pmicontrolesis.menu where cod_menu in(999200,999215,999505,999506,999507);	
    delete from portal.menu_funcionario where ref_cod_menu_submenu in(999200,999215,999505,999506,999507);		
	delete from portal.menu_submenu where cod_menu_submenu in(999200,999215,999505,999506,999507);

	-- //	

