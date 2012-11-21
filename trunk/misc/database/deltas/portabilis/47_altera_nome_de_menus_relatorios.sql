 	-- //
  
 	--
 	-- Altera nome de relatórios para não confundir usuário ao conceder permissão.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  

	update portal.menu_submenu set nm_submenu = 'Relação de Alunos por Turma' where cod_menu_submenu = 999101;
	update pmicontrolesis.menu set tt_menu = 'Relação de Alunos por Turma' where cod_menu = 999101;

  	update portal.menu_submenu set nm_submenu = 'Alunos Matriculados por Escola' where cod_menu_submenu = 999105;
  	update pmicontrolesis.menu set tt_menu = 'Alunos Matriculados por Escola' where cod_menu = 999105;

  	update portal.menu_submenu set nm_submenu = 'Alunos Não Enturmados por Escola' where cod_menu_submenu = 999108;
  	update pmicontrolesis.menu set tt_menu = 'Alunos Não Enturmados por Escola' where cod_menu = 999108;

        update portal.menu_submenu set nm_submenu = 'Gráfico - Alunos Matriculados por Escola' where cod_menu_submenu = 999610;
	update pmicontrolesis.menu set tt_menu = 'Gráfico - Alunos Matriculados por Escola' where cod_menu = 999610;

	update portal.menu_submenu set nm_submenu = 'Gráfico - Alunos que utilizam Transporte' where cod_menu_submenu = 999611;
	update pmicontrolesis.menu set tt_menu = 'Gráfico - Alunos que utilizam Transporte' where cod_menu = 999611;
	 
	-- //@UNDO
  
	update portal.menu_submenu set nm_submenu = 'Relação de Alunos Por Turma' where cod_menu_submenu = 999101;
	update pmicontrolesis.menu set tt_menu = 'Relação de Alunos Por Turma' where cod_menu = 999101;

  	update portal.menu_submenu set nm_submenu = 'Alunos Matriculados Por Escola' where cod_menu_submenu = 999105;
  	update pmicontrolesis.menu set tt_menu = 'Alunos Matriculados Por Escola' where cod_menu = 999105;

  	update portal.menu_submenu set nm_submenu = 'Alunos Não Enturmados Por Escola' where cod_menu_submenu = 999108;
  	update pmicontrolesis.menu set tt_menu = 'Alunos Não Enturmados Por Escola' where cod_menu = 999108;

        update portal.menu_submenu set nm_submenu = 'Alunos Matriculados por Escola' where cod_menu_submenu = 999610;
	update pmicontrolesis.menu set tt_menu = 'Alunos Matriculados por Escola' where cod_menu = 999610;

	update portal.menu_submenu set nm_submenu = 'Alunos que utilizam Transporte' where cod_menu_submenu = 999611;
	update pmicontrolesis.menu set tt_menu = 'Alunos que utilizam Transporte' where cod_menu = 999611;
	  
	-- //


