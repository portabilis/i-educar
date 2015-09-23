 	-- //

 	--
 	-- Cria menus para lançamento de Notas por Alunos e por Turma.
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

	insert into portal.menu_submenu values(643,55,2,'Lançamento por Aluno','educar_falta_nota_aluno_lst.php',NULL,3);	
	insert into portal.menu_funcionario values(1,0,0,643);
	insert into pmicontrolesis.menu values(643,643,21152,'Lançamento por Aluno',3,'educar_falta_nota_aluno_lst.php','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,643,1,0,1);

	update portal.menu_submenu set arquivo = '' where cod_menu_submenu = 642;
	update pmicontrolesis.menu set caminho = '' where ref_cod_menu_submenu = 642;

	insert into portal.menu_submenu values(644,55,2,'Lançamento por Turma','module/Avaliacao/diario',NULL,3);	
	insert into portal.menu_funcionario values(1,0,0,644);
	insert into pmicontrolesis.menu values(644,644,21152,'Lançamento por Turma',4,'module/Avaliacao/diario','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,644,1,0,1);

	-- //@UNDO
	
	delete from  portal.menu_submenu where cod_menu_submenu = 643;
	delete from portal.menu_funcionario where ref_cod_menu_submenu = 643;
	delete from pmicontrolesis.menu where cod_menu = 643;
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 643;
	
	update portal.menu_submenu set arquivo = 'educar_falta_nota_aluno_lst.php' where cod_menu_submenu = 642;
    update pmicontrolesis.menu set caminho = 'educar_falta_nota_aluno_lst.php' where ref_cod_menu_submenu = 642;
	
	delete from  portal.menu_submenu where cod_menu_submenu = 644;
	delete from portal.menu_funcionario where ref_cod_menu_submenu = 644;
	delete from pmicontrolesis.menu where cod_menu = 644;
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 644;
	
	-- //