
-- @author   Paula Bonot <bonot@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (999860, 55, 2,'Relação de docentes e disciplinas lecionadas por turma', 'module/Reports/DocentesDisciplinasPorTurma', null, 3);
insert into portal.menu_funcionario values(1,0,0,999860);
insert into pmicontrolesis.menu values (999860, 999860, 999300, 'Relação de docentes e disciplinas lecionadas por turma', 0, 'module/Reports/DocentesDisciplinasPorTurma', '_self', 1, 15, 192, 1);
insert into pmieducar.menu_tipo_usuario values(1,999860,1,0,1);

-- undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999860;
delete from pmicontrolesis.menu where cod_menu = 999860;
delete from portal.menu_funcionario where ref_cod_menu_submenu = 999860;
delete from portal.menu_submenu where cod_menu_submenu = 999860;