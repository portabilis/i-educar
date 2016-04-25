
-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into pmicontrolesis.menu values(999861, null, 21127, 'Ficha', 0, null, '_self', 1, 15, 19, null);

insert into portal.menu_submenu values(999862,55,2,'Ficha Individual AL','module/Reports/FichaIndividualAL',NULL,3);
insert into pmicontrolesis.menu values(999862,999862,999861,'Ficha Individual AL',0,'module/Reports/FichaIndividualAL','_self',1,15,192);
insert into pmieducar.menu_tipo_usuario values(1,999862,1,1,1);

-- undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999862;
delete from pmicontrolesis.menu where cod_menu = 999862;
delete from portal.menu_submenu where cod_menu_submenu = 999862;

delete from pmicontrolesis.menu where cod_menu = 999861;
