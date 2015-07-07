
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999823, 55, 2,'Atestado de servidor', 'module/Reports/AtestadoServidor', null, 3);
insert into portal.menu_funcionario values(1,0,0,999823);
insert into pmicontrolesis.menu values (999823, 999823, 999400, 'Atestado de servidor', 0, 'module/Reports/AtestadoServidor', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999823,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999823;
delete from pmicontrolesis.menu where cod_menu = 999823;
delete from portal.menu_funcionario where ref_cod_menu_submenu = 999823;
delete from portal.menu_submenu where cod_menu_submenu = 999823;
