
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (999841, 57, 2,'Etiqueta para obras', 'module/Reports/EtiquetaObras', null, 3);
insert into pmicontrolesis.menu values (999841, 999841, 999831, 'Etiqueta para obras', 0, 'module/Reports/EtiquetaObras', '_self', 1, 16, 192);
insert into pmieducar.menu_tipo_usuario values(1,999841,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999841;
delete from pmicontrolesis.menu where cod_menu = 999841;
delete from portal.menu_submenu where cod_menu_submenu = 999841;
