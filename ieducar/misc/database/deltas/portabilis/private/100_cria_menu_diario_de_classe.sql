
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999816, 55, 2 ,'Diário de classe', 'module/Reports/DiarioDeClasse', null, 3);
insert into pmicontrolesis.menu values (999816, 999816, 999500, 'Diário de classe', 0, 'module/Reports/DiarioDeClasse', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999816,1,1,1);

--undo

delete from portal.menu_submenu where cod_menu_submenu = 999816;
delete from pmicontrolesis.menu where cod_menu = 999816;