
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999819, 55, 2,'Ficha de acompanhamento do aluno', 'module/Reports/FichaAcompanhamentoAluno', null, 3);
insert into portal.menu_funcionario values(1,0,0,999819);
insert into pmicontrolesis.menu values (999819, 999819, 999450, 'Ficha de acompanhamento do aluno', 0, 'module/Reports/FichaAcompanhamentoAluno', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999819,1,0,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999819;
delete from pmicontrolesis.menu where cod_menu = 999819;
delete from portal.menu_funcionario where ref_cod_menu_submenu = 999819;
delete from portal.menu_submenu where cod_menu_submenu = 999819;
