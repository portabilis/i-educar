--
-- Reorganiza os menus e efetua padronizações:
---- Seta a nomenclatura dos menus como primeira maiuscula e o resto minuscula;
---- Move o menu relatório de conferência de notas e faltas para os relatórios cadastrais;
---- Adiciona o campo tipo_menu em pmicontrolesis.menu onde 1 = Administrador / 2 = Geral;
---- Ajusta o menu de registro de síntese de comp. e habilidades;
---- Seta o ordenamento do menu = 0, menos nos menus principais;
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

update portal.menu_submenu set nm_submenu = upper(substr(nm_submenu, 1, 1)) || lower(substr(nm_submenu,2,length(nm_submenu) - 1));
update pmicontrolesis.menu set tt_menu = upper(substr(tt_menu, 1, 1)) || lower(substr(tt_menu,2,length(tt_menu) - 1));

update pmicontrolesis.menu set ref_cod_menu_pai = 999300 where cod_menu = 999809;

alter table pmicontrolesis.menu add tipo_menu integer;

update pmicontrolesis.menu set tipo_menu = 1 where cod_menu in(999201,999237,999218,999605,999231,999223,999110,999107,999610,999654,999652,999651,999612);
update pmicontrolesis.menu set tipo_menu = 2 where cod_menu in(999809,999217,999607,999105,999227,999233,999234,999235,999238,999108,999230,999230,999228,
							       							   999203,999204,999220,999221,999109,999101,999225,999606,999219,999224,999105,999611);

update portal.menu_submenu set nm_submenu = 'Registro síntese de comp. e habilidades' where cod_menu_submenu = 999805;
update pmicontrolesis.menu set tt_menu = 'Registro síntese de comp. e habilidades' where cod_menu = 999805;

update pmicontrolesis.menu set ord_menu = 0 where ref_cod_menu_pai is not null;