
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into pmicontrolesis.menu values(999850, null, 21127, 'Termos', 0, null, '_self', 1, 15, 19, null);

insert into portal.menu_submenu values(999851,55,2,'Termo de ausência','module/Reports/TermoAusencia',NULL,3);
insert into pmicontrolesis.menu values(999851,999851,999850,'Termo de ausência',0,'module/Reports/TermoAusencia','_self',1,15,192);
insert into pmieducar.menu_tipo_usuario values(1,999851,1,1,1);

-- undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999851;
delete from pmicontrolesis.menu where cod_menu = 999851;
delete from portal.menu_submenu where cod_menu_submenu = 999851;

delete from pmicontrolesis.menu where cod_menu = 999850;
