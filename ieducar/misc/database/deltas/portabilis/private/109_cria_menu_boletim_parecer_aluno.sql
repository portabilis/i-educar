
-- @author   Alan Felipe Farias <alan@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values (999823, 55, 2,'Parecer descritivo por etapa', 'module/Reports/BoletimParecerAluno', null, 3);
insert into pmicontrolesis.menu values (999823, 999823, 999450, 'Parecer descritivo por etapa', 0, 'module/Reports/BoletimParecerAluno', '_self', 1, 15, 192, null);
insert into pmieducar.menu_tipo_usuario values(1,999823,1,1,1);

--undo

delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999823;
delete from pmicontrolesis.menu where cod_menu = 999823;
delete from portal.menu_submenu where cod_menu_submenu = 999823;

