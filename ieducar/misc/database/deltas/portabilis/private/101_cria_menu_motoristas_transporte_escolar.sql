-- @author   Isac Borgert <isac@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999817, 69, 2 ,'Motoristas de transporte escolar', 'module/Reports/RelatorioMotorista', null, 3);
insert into pmicontrolesis.menu values (999817, 999817, 20712, 'Motoristas de transporte escolar', 0, 'module/Reports/RelatorioMotorista', '_self', 1, 17, 192);
insert into pmieducar.menu_tipo_usuario values(1,999817,1,1,1);

--undo

delete from portal.menu_submenu where cod_menu_submenu = 999817;
delete from pmicontrolesis.menu where cod_menu = 999817;