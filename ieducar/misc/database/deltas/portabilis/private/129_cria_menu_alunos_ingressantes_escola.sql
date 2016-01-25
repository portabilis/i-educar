
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values(999854,55,2,'Alunos ingressantes','module/Reports/AlunosIngressantes',NULL,3);
insert into pmicontrolesis.menu values(999854,999854,999300,'Alunos ingressantes',0,'module/Reports/AlunosIngressantes','_self',1,15,192);
insert into pmieducar.menu_tipo_usuario values(1,999854,1,1,1);

-- undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999854;
delete from pmicontrolesis.menu where cod_menu = 999854;
delete from portal.menu_submenu where cod_menu_submenu = 999854;
