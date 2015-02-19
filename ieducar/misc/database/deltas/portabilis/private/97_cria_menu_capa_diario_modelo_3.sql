
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999815, 55, 2 ,'Diário de classe - capa (modelo 3)', 'module/Reports/DiarioClasseCapaModelo3', null, 3);
insert into pmicontrolesis.menu values (999815, 999815, 999500, 'Diário de classe - capa (modelo 3)', 0, 'module/Reports/DiarioClasseCapaModelo3', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999815,1,1,1);

--undo

delete from portal.menu_submenu where cod_menu_submenu = 999815;
delete from pmicontrolesis.menu where cod_menu = 999815;