-- Cria menu para diário de classe - retrato
-- @author   Caroline Salib <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values(999855,55,2,'Diário de classe - Retrato','module/Reports/DiarioClasseRetrato',NULL,3);
insert into pmicontrolesis.menu values(999855,999855,999500,'Diário de classe - Retrato',8,'module/Reports/DiarioClasseRetrato','_self',1,15,192);
insert into pmieducar.menu_tipo_usuario values(1,999855,1,1,1);

--UNDO

delete from pmicontrolesis.menu where cod_menu = 999855;
delete from portal.menu_submenu where cod_menu_submenu = 999855;
