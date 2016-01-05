
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values(999853,55,2,'Termo de desistência de vaga','module/Reports/TermoDesistenciaVaga',NULL,3);
insert into pmicontrolesis.menu values(999853,999853,999850,'Termo de desistência de vaga',0,'module/Reports/TermoDesistenciaVaga','_self',1,15,192);
insert into pmieducar.menu_tipo_usuario values(1,999853,1,1,1);

-- undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999853;
delete from pmicontrolesis.menu where cod_menu = 999853;
delete from portal.menu_submenu where cod_menu_submenu = 999853;
