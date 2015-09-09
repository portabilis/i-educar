--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

UPDATE portal.menu_submenu SET arquivo = 'educar_componente_curricular_lst.php' WHERE cod_menu_submenu = 946;
UPDATE pmicontrolesis.menu SET caminho = 'educar_componente_curricular_lst.php' WHERE cod_menu = 21195;

-- undo

UPDATE portal.menu_submenu SET arquivo = 'module/ComponenteCurricular/index' WHERE cod_menu_submenu = 946;
UPDATE pmicontrolesis.menu SET caminho = 'module/ComponenteCurricular/index' WHERE cod_menu = 21195;