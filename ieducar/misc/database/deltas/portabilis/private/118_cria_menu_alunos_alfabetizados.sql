
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999833, 55, 2,'Relação de alunos alfabetizados', 'module/Reports/AlunosAlfabetizados', null, 3);
insert into portal.menu_funcionario values(1,0,0,999833);
insert into pmicontrolesis.menu values (999833, 999833, 999300, 'Relação de alunos alfabetizados', 0, 'module/Reports/AlunosAlfabetizados', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999833,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999833;
delete from pmicontrolesis.menu where cod_menu = 999833;
delete from portal.menu_funcionario where ref_cod_menu_submenu = 999833;
delete from portal.menu_submenu where cod_menu_submenu = 999833;
