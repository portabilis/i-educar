
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (999848, 55, 2,'Bloqueio de lançamento de notas e faltas por etapa', 'educar_bloqueio_lancamento_faltas_notas_lst.php', null, 3);
insert into pmicontrolesis.menu values (999848, 999848, 21122, 'Bloqueio de lançamento de notas e faltas por etapa', 0, 'educar_bloqueio_lancamento_faltas_notas_lst.php', '_self', 1, 16, 192);
insert into pmieducar.menu_tipo_usuario values(1,999848,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999848;
delete from pmicontrolesis.menu where cod_menu = 999848;
delete from portal.menu_submenu where cod_menu_submenu = 999848;
