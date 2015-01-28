-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values (21248, 55, 2, 'Demandas x disponibilidade de vagas na ed. infantil', 'module/Reports/DemandasDisponibilidadeVagasInfantil', null, 3);

insert into pmicontrolesis.menu values (21248, 21248, 999300, 'Demandas x disponibilidade de vagas na ed. infantil', 0, 'module/Reports/DemandasDisponibilidadeVagasInfantil', '_self', 1, 15, 192, 1);

insert into portal.menu_funcionario values(1,1,1,21248);

insert into pmieducar.menu_tipo_usuario values(1,21248,1,1,1);


---Reverse
--delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 21248;

--delete from portal.menu_funcionario where ref_cod_menu_submenu = 21248;

--delete from pmicontrolesis.menu where cod_menu = 21248;

--delete from portal.menu_submenu where cod_menu_submenu = 21248;


