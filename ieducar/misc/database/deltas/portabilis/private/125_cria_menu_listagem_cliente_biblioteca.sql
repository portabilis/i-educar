
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (999845, 57, 2,'Relação de clientes', 'module/Reports/RelacaoClientesBiblioteca', null, 3);
insert into pmicontrolesis.menu values (999845, 999845, 999614, 'Relação de clientes', 0, 'module/Reports/RelacaoClientesBiblioteca', '_self', 1, 16, 192);
insert into pmieducar.menu_tipo_usuario values(1,999845,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999845;
delete from pmicontrolesis.menu where cod_menu = 999845;
delete from portal.menu_submenu where cod_menu_submenu = 999845;
