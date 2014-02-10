	-- //

	--
	-- Cria menu de relatórios faltantes nos deltas anteriores
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
	-- @license  @@license@@
	-- @version  $Id$  

	insert into portal.menu_submenu values(999205,55,2,'Boletim do Professor','module/Reports/BoletimProfessor',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999205);
	insert into pmicontrolesis.menu values(999205,999205,999450,'Boletim do Professor',5,'module/Reports/BoletimProfessor','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,999205,1,0,1);

	insert into portal.menu_submenu values(999105,55,2,'Alunos Matriculados por Escola','module/Reports/MatriculaEscola',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999105);
	insert into pmicontrolesis.menu values(999105,999105,999300,'Alunos Matriculados por Escola',1,'module/Reports/MatriculaEscola','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,999105,1,0,1);	

	insert into portal.menu_submenu values(999101,55,2,'Relação de Alunos por Turma','module/Reports/AlunoTurma',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999101);
	insert into pmicontrolesis.menu values(999101,999101,999300,'Relação de Alunos por Turma',6,'module/Reports/AlunoTurma','_self',1,15,155);
	insert into pmieducar.menu_tipo_usuario values(1,999101,1,0,1);	

	insert into portal.menu_submenu values(999201,55,2,'Movimento de Alunos','module/Reports/MovimentoAlunos',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999201);
	insert into pmicontrolesis.menu values(999201,999201,999301,'Movimento de Alunos',2,'module/Reports/MovimentoAlunos','_self',1,15,127);
	insert into pmieducar.menu_tipo_usuario values(1,999201,1,0,1);

	insert into portal.menu_submenu values(999204,55,2,'Ficha do Aluno em Branco','module/Reports/FichaAlunoBranco',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999204);
	insert into pmicontrolesis.menu values(999204,999204,999301,'Ficha do Aluno em Branco',4,'module/Reports/FichaAlunoBranco','_self',1,15,192);
	insert into pmieducar.menu_tipo_usuario values(1,999204,1,0,1);