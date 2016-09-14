-- Cria menu para registro de classe conceitual
-- @author   Lucas Schmoeller da Silva <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

insert into portal.menu_submenu values(999875, 55, 2, 'Registro de avaliação conceitual', 'module/Reports/RegistroAvaliacaoConceitual', NULL, 3);
insert into pmicontrolesis.menu values(999875, 999875, 999500, 'Registro de avaliação conceitual', 8, 'module/Reports/RegistroAvaliacaoConceitual', '_self', 1, 15, 192);
insert into pmieducar.menu_tipo_usuario values(1,999875,1,1,1);