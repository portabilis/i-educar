--
-- Cria o menu para a declaração de conclusão de curso
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


insert into pmicontrolesis.menu values( 999811, null, 21127, 'Declarações', 0, null, '_self', 1, 15, 21, null);
insert into portal.menu_submenu values( 999812, 55, 2, 'Declaração de conclusão de curso', 'module/Reports/DeclaracaoConclusaoCurso', null, 3);
insert into pmicontrolesis.menu values( 999812, 999812, 999811, 'Declaração de conclusão de curso', 0, 'module/Reports/DeclaracaoConclusaoCurso', '_self', 1, 15, 21, null);

-- //@UNDO

delete from pmicontrolesis.menu where cod_menu in(999811, 999812);
delete from portal.menu_submenu where cod_menusubmenu = 999812;