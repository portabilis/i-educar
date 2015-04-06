-- @author   Isac Borgert <isac@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (21252, 69, 2 ,'Motoristas de transporte escolar', 'module/Reports/RelatorioMotorista', null, 3);
insert into pmicontrolesis.menu values (21252, 21252, 20712, 'Motoristas de transporte escolar', 0, 'module/Reports/RelatorioMotorista', '_self', 1, 17, 192);
insert into pmieducar.menu_tipo_usuario values(1,21252,1,1,1);

--undo

delete from portal.menu_submenu where cod_menu_submenu = 21252;
delete from pmicontrolesis.menu where cod_menu = 21252;