--
-- Cria menu para relatório de frequência de alunos por escola
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values(999813, 55, 2, 'Frequência de alunos por escola', 'module/Reports/FrequenciaEscola', null, 3);
insert into pmicontrolesis.menu values(999813,999813, 999300, 'Frequência de alunos por escola', 0, 'module/Reports/FrequenciaEscola', '_self', 1, 15, 155, 2);

-- //@UNDO

delete from pmicontrolesis.menu where cod_menu = 999813;
delete from portal.menu_submenu where cod_menu_submenu = 999813;