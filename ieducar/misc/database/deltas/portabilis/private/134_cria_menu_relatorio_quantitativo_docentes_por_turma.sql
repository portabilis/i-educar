
-- @author   Paula Bonot <bonot@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into pmicontrolesis.menu values(999858,NULL,21126,'Quantitativos',0,NULL,'_self',1,15,17);

insert into portal.menu_submenu values (999859, 55, 2,'Relatório quantitativo de docentes por turma', 'module/Reports/DocentesPorTurma', null, 3);
insert into portal.menu_funcionario values(1,0,0,999859);
insert into pmicontrolesis.menu values (999859, 999859, 999858, 'Relatório quantitativo de docentes por turma', 0, 'module/Reports/DocentesPorTurma', '_self', 1, 15, 192, 1);
insert into pmieducar.menu_tipo_usuario values(1,999859,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999859;
delete from pmicontrolesis.menu where cod_menu = 999859;
delete from portal.menu_funcionario where ref_cod_menu_submenu = 999859;
delete from portal.menu_submenu where cod_menu_submenu = 999859;

delete from pmicontrolesis.menu where cod_menu = 999858;
