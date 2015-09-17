
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999834, 55, 2,'Comparativo de médias por etapa', 'module/Reports/MediaAlunos', null, 3);
insert into pmicontrolesis.menu values (999834, 999834, 999300, 'Comparativo de médias por etapa', 0, 'module/Reports/MediaAlunos', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999834,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999834;
delete from pmicontrolesis.menu where cod_menu = 999834;
delete from portal.menu_submenu where cod_menu_submenu = 999834;

