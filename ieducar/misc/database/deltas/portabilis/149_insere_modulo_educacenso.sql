-- 
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

INSERT INTO portal.menu_menu (cod_menu_menu, nm_menu)
        VALUES (70,'Educacenso');

DELETE FROM pmicontrolesis.menu WHERE cod_menu = 846;
DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 846;

INSERT INTO portal.menu_submenu (cod_menu_submenu, ref_cod_menu_menu, cod_sistema, nm_submenu, arquivo, nivel)
        VALUES (846, 70, 2, 'Exportação', 'educar_exportacao_educacenso.php', '2');

INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,846, 1, 1, 1);