--
-- Cria o menu para o relatório de conferência de notas e faltas.
-- @author   Gabriel Matos de Souza <Gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values(999809, 55, 2, 'Conferência de notas e faltas', 'module/Reports/ConferenciaNotasFaltas', null, 3);
insert into pmicontrolesis.menu values(999809, 999809, 999301, 'Conferência de notas e faltas', 1, 'module/Reports/ConferenciaNotasFaltas', '_self', 1, 15, 192);

-- //@UNDO

delete from pmicontrolesis.menu where cod_menu = 999809;
delete from portal.menu_submenu where cod_menu_submenu = 999809;
