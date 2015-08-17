
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999829, 55, 2,'Histórico escolar educação infantil', 'module/Reports/HistoricoEscolarEducacaoInfantil', null, 3);
insert into pmicontrolesis.menu values (999829, 999829, 999460, 'Histórico escolar educação infantil', 0, 'module/Reports/HistoricoEscolarEducacaoInfantil', '_self', 1, 15, 122, null);
insert into pmieducar.menu_tipo_usuario values(1,999829,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999829;
delete from pmicontrolesis.menu where cod_menu = 999829;
delete from portal.menu_submenu where cod_menu_submenu = 999829;

