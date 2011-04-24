 	-- //

 	--
 	-- Cria o Boletim Trimestral e organiza os menus.
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

	update portal.menu_submenu set nm_submenu = 'Boletim Escolar - Bimestral' where cod_menu_submenu = 999202;
	update pmicontrolesis.menu set tt_menu = 'Boletim Escolar - Bimestral' where cod_menu = 999202;

	update pmicontrolesis.menu set ord_menu = 4 where cod_menu = 999205;

	insert into portal.menu_submenu values(999218,55,2,'Boletim Escolar - Trimestral','portabilis_boletim_trimestral.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999218);
	insert into pmicontrolesis.menu values(999218,999218,999450,'Boletim Escolar - Trimestral',3,'portabilis_boletim_trimestral.php','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,999218,1,0,1);
	
	-- //@UNDO
	
	update portal.menu_submenu set nm_submenu = 'Boletim Escolar' where cod_menu_submenu = 999202;
	update pmicontrolesis.menu set tt_menu = 'Boletim Escolar' where cod_menu = 999202;

	update pmicontrolesis.menu set ord_menu = 2 where cod_menu = 999205;
		
	delete from  portal.menu_submenu where cod_menu_submenu = 999205;
	delete from portal.menu_funcionario where ref_cod_menu_submenu = 999205;
	delete from pmicontrolesis.menu where cod_menu = 999205;
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999205;
	
	-- //	