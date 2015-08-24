
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999830, 55, 2,'Rendimento e movimento escolar', 'module/Reports/RendimentoMovimentoEscolar', null, 3);
insert into pmicontrolesis.menu values (999830, 999830, 999300, 'Rendimento e movimento escolar', 0, 'module/Reports/RendimentoMovimentoEscolar', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999830,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999830;
delete from pmicontrolesis.menu where cod_menu = 999830;
delete from portal.menu_submenu where cod_menu_submenu = 999830;

