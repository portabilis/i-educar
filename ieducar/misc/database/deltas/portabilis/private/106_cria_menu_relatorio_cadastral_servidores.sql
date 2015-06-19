
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999820, 55, 2,'Relatório cadastral de servidores', 'module/Reports/RelacaoServidores', null, 3);
insert into portal.menu_funcionario values(1,0,0,999820);
insert into pmicontrolesis.menu values (999820, 999820, 999302, 'Relatório cadastral de servidores', 0, 'module/Reports/RelacaoServidores', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999820,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999820;
delete from pmicontrolesis.menu where cod_menu = 999820;
delete from portal.menu_funcionario where ref_cod_menu_submenu = 999820;
delete from portal.menu_submenu where cod_menu_submenu = 999820;
