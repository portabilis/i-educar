
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999819, 55, 2,'Ficha acompanhamento aluno', 'module/Reports/FichaAcompanhamentoAluno', null, 3);
insert into pmicontrolesis.menu values (999819, 999819, 999300, 'Ficha acompanhamento aluno', 0, 'module/Reports/FichaAcompanhamentoAluno', '_self', 1, 15, 192, 2);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999819;
delete from portal.menu_submenu where cod_menu_submenu = 999819;
delete from pmicontrolesis.menu where cod_menu = 999819;