-- @author   Isac Borgert <isac@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (21247, 55, 2, 'Relação de alunos e localidade', 'module/Reports/AlunosLocalidade', null, 3);

insert into pmicontrolesis.menu values (21247, 21247, 999300, 'Relação de alunos e localidade', 0, 'module/Reports/AlunosLocalidade', '_self', 1, 15, 192, 2);

insert into portal.menu_funcionario values(1,0,0,21247);

insert into pmieducar.menu_tipo_usuario values(1,21247,1,0,1);


---Reverse
--delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 21247;

--delete from portal.menu_funcionario where ref_cod_menu_submenu = 21247;

--delete from pmicontrolesis.menu where cod_menu = 21247;

--delete from portal.menu_submenu where cod_menu_submenu = 21247;


