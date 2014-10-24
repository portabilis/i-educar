--
-- Cria o menu para o relatório de carteira do professor.
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into portal.menu_submenu values(999806, 55, 2, 'Atestado de Abandono', 'module/Reports/AtestadoAbandono', null, 3 );
insert into pmicontrolesis.menu values(999806, 999806, 999400, 'Atestado de Abandono', 4, 'module/Reports/AtestadoAbandono', '_self', 1, 15, 192);

-- //@UNDO

delete from pmicontrolesis.menu where cod_menu = 999806;
delete from portal.menu_submenu where cod_menu_submenu = 999806;