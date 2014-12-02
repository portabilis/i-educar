--
-- Cria o menu para o relatório de atestado de escolaridade.
-- @author   Gabriel Matos de Souza <Gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values(999810, 55, 2, 'Atestado de escolaridade', 'module/Reports/AtestadoEscolaridade', null, 3);
insert into pmicontrolesis.menu values(999810, 999810, 999400, 'Atestado de escolaridade', 0, 'module/Reports/AtestadoEscolaridade', '_self', 1, 15, 192, null);

-- //@UNDO

delete from pmicontrolesis.menu where cod_menu = 999810;
delete from portal.menu_submenu where cod_menu_submenu = 999810;
