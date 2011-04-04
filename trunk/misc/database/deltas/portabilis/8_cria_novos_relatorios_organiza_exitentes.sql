 	-- //

 	--
 	-- Cria menu Servidores, organiza os menus e adiciona ícones, cria relatórios: Atestado de Transferência, 
	-- Horas Alocadas por Servidor e Ocorrências Disciplinares por Aluno.
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

	insert into pmicontrolesis.menu values(999302,NULL,21126,'Servidores',3,NULL,'_self',1,15,37);

	update pmicontrolesis.menu set ord_menu = 2 where cod_menu = 999450;
	update pmicontrolesis.menu set ord_menu = 3 where cod_menu = 999460;

	ALTER TABLE pmieducar.historico_escolar ADD COLUMN frequencia double precision;
	ALTER TABLE pmieducar.historico_escolar ALTER COLUMN frequencia SET STORAGE PLAIN;

	insert into portal.menu_submenu values(999216,55,2,'Atestado de Transferência','portabilis_atestado_transferencia.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999216);
	insert into pmicontrolesis.menu values(999216,999216,999400,'Atestado de Transferência',2,'portabilis_atestado_transferencia.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999216,1,0,1);

	insert into portal.menu_submenu values(999107,55,2,'Horas Alocadas por Servidor','portabilis_servidores_horas_alocadas.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999107);
	insert into pmicontrolesis.menu values(999107,999107,999302,'Horas Alocadas por Servidor',2,'portabilis_servidores_horas_alocadas.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999107,1,0,1);

	insert into portal.menu_submenu values(999217,55,2,'Ocorrências Disciplinares por Aluno','portabilis_alunos_ocorrencias_disciplinares.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999217);
	insert into pmicontrolesis.menu values(999217,999217,999301,'Ocorrências Disciplinares por Aluno',4,'portabilis_alunos_ocorrencias_disciplinares.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999217,1,0,1);
 	
	update pmicontrolesis.menu set ref_cod_ico = 192 where ref_cod_ico is null;
	-- //@UNDO
	
	ALTER TABLE pmieducar.historico_escolar DROP COLUMN frequencia;
	
	delete from portal.menu_submenu where cod_menu_submenu = 999216;
	delete from portal.menu_funcionario where ref_cod_menu_submenu = 999216;
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999216;
	delete from pmicontrolesis.menu where cod_menu = 999216;

	delete from portal.menu_submenu where cod_menu_submenu = 999107;
	delete from portal.menu_funcionario where ref_cod_menu_submenu = 999107;
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999107;
	delete from pmicontrolesis.menu where cod_menu = 999107;

	delete from portal.menu_submenu where cod_menu_submenu = 999217;
	delete from portal.menu_funcionario where ref_cod_menu_submenu = 999217;
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999217;
	delete from pmicontrolesis.menu where cod_menu = 999217;
	
	-- //