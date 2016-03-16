
-- @author   Paula Bonot <bonot@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (999856, 57, 2,'Empréstimos em atraso', 'module/Reports/EmprestimoAtrasoBiblioteca', null, 3);
insert into pmicontrolesis.menu values (999856, 999856, 999614, 'Empréstimos em atraso', 0, 'module/Reports/EmprestimoAtrasoBiblioteca', '_self', 1, 16, 192);
insert into pmieducar.menu_tipo_usuario values(1,999856,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999856;
delete from pmicontrolesis.menu where cod_menu = 999856;
delete from portal.menu_submenu where cod_menu_submenu = 999856;
