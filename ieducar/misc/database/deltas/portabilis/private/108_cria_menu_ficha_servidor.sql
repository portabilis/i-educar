
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999822, 55, 2,'Ficha do Servidor', 'module/Reports/FichaServidor', null, 3);
insert into pmicontrolesis.menu values (999822, 999822, 999300, 'Ficha do Servidor', 0, 'module/Reports/FichaServidor', '_self', 1, 15, 192, 1);
insert into pmieducar.menu_tipo_usuario values(1,999822,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999822;
delete from pmicontrolesis.menu where cod_menu = 999822;
delete from portal.menu_submenu where cod_menu_submenu = 999822;

