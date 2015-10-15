
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999842, 55, 2,'Gráfico comparativo de médias por disciplina', 'module/Reports/GraficoMediasDisciplina', null, 3);
insert into pmicontrolesis.menu values (999842, 999842, 999303, 'Gráfico comparativo de médias por disciplina', 0, 'module/Reports/GraficoMediasDisciplina', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999842,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999842;
delete from pmicontrolesis.menu where cod_menu = 999842;
delete from portal.menu_submenu where cod_menu_submenu = 999842;

