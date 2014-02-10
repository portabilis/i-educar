	-- //

 	--
 	-- Cria menu Carteiras, organiza os menus.
	-- Cria relatórios: Carteira de Estudante e Carteira de Transporte.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
	
	INSERT INTO pmicontrolesis.menu VALUES (999301,null,21126, 'Movimentos', 2, null, '_self', 1, 15, 31);
	
	insert into pmicontrolesis.menu values(999600,NULL,21127,'Carteiras',2,NULL,'_self',1,15,20);

	insert into portal.menu_submenu values(999601,55,2,'Carteira de Transporte','portabilis_carteira_transporte.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999601);
	insert into pmicontrolesis.menu values(999601,999601,999600,'Carteira de Transporte',4,'portabilis_carteira_transporte.php','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,999601,1,0,1);

	insert into portal.menu_submenu values(999602,55,2,'Carteira de Estudante','portabilis_carteira_estudante.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999602);
	insert into pmicontrolesis.menu values(999602,999602,999600,'Carteira de Estudante',3,'portabilis_carteira_estudante.php','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,999602,1,0,1);

	update pmicontrolesis.menu set ord_menu = 5 where cod_menu = 999460;
	update pmicontrolesis.menu set ord_menu = 6 where cod_menu = 999500;
	
	-- //@UNDO
	
	delete from pmicontrolesis.menu where cod_menu = 999600;
	
	delete from portal.menu_submenu where cod_menu_submenu = 999601;
	delete from portal.menu_funcionario where ref_cod_menu_submenu = 999601;
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999601;
	delete from pmicontrolesis.menu where cod_menu = 999601;

	delete from portal.menu_submenu where cod_menu_submenu = 999602;
	delete from portal.menu_funcionario where ref_cod_menu_submenu = 999602;
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999602;
	delete from pmicontrolesis.menu where cod_menu = 999602;
	
	-- //