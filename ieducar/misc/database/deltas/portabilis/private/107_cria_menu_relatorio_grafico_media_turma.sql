
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999821, 55, 2,'Gráfico desempenho da média da turma', 'module/Reports/DesempenhoMediaTurma', null, 3);
insert into pmicontrolesis.menu values (999821, 999821, 999303, 'Gráfico desempenho da média da turma', 0, 'module/Reports/DesempenhoMediaTurma', '_self', 1, 15, 192, 1);
insert into pmieducar.menu_tipo_usuario values(1,999821,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999821;
delete from pmicontrolesis.menu where cod_menu = 999821;
delete from portal.menu_submenu where cod_menu_submenu = 999821;

