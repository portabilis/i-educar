
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999843, 55, 2,'Relação de anos letivos por escola', 'module/Reports/AnosLetivosEscola', null, 3);
insert into pmicontrolesis.menu values (999843, 999843, 999301, 'Relação de anos letivos por escola', 0, 'module/Reports/AnosLetivosEscola', '_self', 1, 15, 127);
insert into pmieducar.menu_tipo_usuario values(1,999843,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999843;
delete from pmicontrolesis.menu where cod_menu = 999843;
delete from portal.menu_submenu where cod_menu_submenu = 999843;