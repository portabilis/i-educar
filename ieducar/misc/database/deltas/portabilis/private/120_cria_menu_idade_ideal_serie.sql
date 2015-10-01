
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (999840, 55, 2,'Gráfico de distorção idade/série', 'module/Reports/GraficoDistorcaoIdadeSerie', null, 3);
insert into pmicontrolesis.menu values (999840, 999840, 999303, 'Gráfico de distorção idade/série', 0, 'module/Reports/GraficoDistorcaoIdadeSerie', '_self', 1, 15, 192, 1);
insert into pmieducar.menu_tipo_usuario values(1,999840,1,1,1);

-- undo
delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999840;
delete from pmicontrolesis.menu where cod_menu = 999840;
delete from portal.menu_submenu where cod_menu_submenu = 999840;
