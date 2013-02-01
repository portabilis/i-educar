 	-- //

 	--
 	-- Ajusta ordem de menu existente de acordo com os novos inseridos.
	-- Cria os modelos: Boletim Escolar - Parecer Descritivo e Alunos Não Enturmados por Escola.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

	update pmicontrolesis.menu set ord_menu = 3 where cod_menu = 999218;

	insert into portal.menu_submenu values(999219,55,2,'Boletim Escolar - Parecer Descritivo','portabilis_boletim_parecer.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999219);
	insert into pmicontrolesis.menu values(999219,999219,999450,'Boletim Escolar - Parecer Descritivo',2,'portabilis_boletim_parecer.php','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,999219,1,0,1);	

	insert into portal.menu_submenu values(999108,55,2,'Alunos Não Enturmados por Escola','portabilis_alunos_nao_enturmados_por_escola.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999108);
	insert into pmicontrolesis.menu values(999108,999108,999300,'Alunos Não Enturmados por Escola',5,'portabilis_alunos_nao_enturmados_por_escola.php','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,999108,1,0,1);	

	-- //@UNDO
	
	update pmicontrolesis.menu set ord_menu = 2 where cod_menu = 999218;
	
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999219;
	delete from pmicontrolesis.menu where cod_menu = 999219;
    delete from portal.menu_funcionario where ref_cod_menu_submenu = 999219;
	delete from  portal.menu_submenu where cod_menu_submenu = 999219;
	
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999108;
	delete from pmicontrolesis.menu where cod_menu = 999108;
    delete from portal.menu_funcionario where ref_cod_menu_submenu = 999108;
	delete from  portal.menu_submenu where cod_menu_submenu = 999108;
	
	-- //