--
-- 
-- @author   Isac Borgert <isac@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$
update pmicontrolesis.menu set tt_menu = 'Gráfico - alunos que utilizam transporte' where cod_menu = 999611;

update portal.menu_submenu set nm_submenu = 'Gráfico - alunos que utilizam transporte' where cod_menu_submenu = 999611;
