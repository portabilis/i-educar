-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values(999864,55,2,'Ficha Individual AL (6º a 9º ANO)','module/Reports/FichaIndividualAL6a9',NULL,3);
insert into pmicontrolesis.menu values(999864,999864,999861,'Ficha Individual AL (6º a 9º ANO)',0,'module/Reports/FichaIndividualAL6a9','_self',1,15,192);
insert into pmieducar.menu_tipo_usuario values(1,999864,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999864;
delete from pmicontrolesis.menu where cod_menu = 999864;
delete from portal.menu_submenu where cod_menu_submenu = 999864;