
-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (999857, 55, 2,'Relação de alunos por bairro', 'module/Reports/RelacaoAlunosBairro', null, 3);
insert into pmicontrolesis.menu values (999857, 999857, 999300, 'Relação de alunos por bairro', 0, 'module/Reports/RelacaoAlunosBairro', '_self', 1, 16, 192);
insert into pmieducar.menu_tipo_usuario values(1,999857,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999857;
delete from pmicontrolesis.menu where cod_menu = 999857;
delete from portal.menu_submenu where cod_menu_submenu = 999857;