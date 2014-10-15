--
-- Cria o menu para o relatório de carteira do professor.
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values(999603, 55, 2, 'Carteira do Professor', 'module/Reports/CarteiraProfessor', null, 3);
insert into pmicontrolesis.menu values(999603, 999603, 999600, 'Carteira do Professor', 5, 'module/Reports/CarteiraProfessor', '_self', 1, 15, 192);

-- //@UNDO

delete from pmicontrolesis.menu where cod_menu = 999603;
delete from portal.menu_submenu where cod_menu_submenu = 999603;