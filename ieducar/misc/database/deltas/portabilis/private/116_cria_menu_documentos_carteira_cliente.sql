
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into pmicontrolesis.menu values (999831, null, null, 'Documentos', 4, null, '_self', 1, 16, 1, null);
insert into portal.menu_submenu values (999832, 55, 2,'Carteira de cliente', 'module/Reports/CarteiraCliente', null, 3);
insert into pmicontrolesis.menu values (999832, 999832, 999831, 'Carteira de cliente', 0, 'module/Reports/CarteiraCliente', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999832,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999832;
delete from pmicontrolesis.menu where cod_menu = 999832;
delete from portal.menu_submenu where cod_menu_submenu = 999832;
delete from pmicontrolesis.menu where cod_menu = 999831;

