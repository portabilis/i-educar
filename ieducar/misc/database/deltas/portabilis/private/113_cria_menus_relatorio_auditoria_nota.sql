
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into pmicontrolesis.menu values(999827,NULL,21126,'Auditoria',0,NULL,'_self',1,15,17);

insert into portal.menu_submenu values (999828, 55, 2,'Alteração em notas', 'module/Reports/AuditoriaNotas', null, 3);
insert into portal.menu_funcionario values(1,0,0,999828);
insert into pmicontrolesis.menu values (999828, 999828, 999827, 'Alteração em notas', 0, 'module/Reports/AuditoriaNotas', '_self', 1, 15, 192, 1);
insert into pmieducar.menu_tipo_usuario values(1,999828,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999828;
delete from pmicontrolesis.menu where cod_menu = 999828;
delete from portal.menu_funcionario where ref_cod_menu_submenu = 999828;
delete from portal.menu_submenu where cod_menu_submenu = 999828;

delete from pmicontrolesis.menu where cod_menu = 999827;
