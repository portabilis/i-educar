
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999825, 69, 2 ,'Relação de usu&aacute;rios de transporte', 'module/Reports/RelacaoUsuarioTransporte', null, 3);
insert into portal.menu_funcionario values(1,0,0,999825);
insert into pmicontrolesis.menu values (999825, 999825, 20712, 'Relação de usu&aacute;rios de transporte', 0, 'module/Reports/RelacaoUsuarioTransporte', '_self', 1, 17, 192);
insert into pmieducar.menu_tipo_usuario values(1,999825,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999825;
delete from pmicontrolesis.menu where cod_menu = 999825;
delete from portal.menu_submenu where cod_menu_submenu = 999825;
delete from portal.menu_funcionario where ref_cod_menu_submenu = 999825;