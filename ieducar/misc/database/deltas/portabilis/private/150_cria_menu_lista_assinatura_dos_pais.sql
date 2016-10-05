-- Cria menu de lista de assisnaturas dos pais
-- @author   Paula Bonot <bonot@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

INSERT INTO portal.menu_submenu VALUES (999870, 55, 2,'Lista de alunos para assinatura dos pais', 'module/Reports/AssinaturaDosPais', null, 3);
INSERT INTO pmicontrolesis.menu VALUES (999870, 999870, 999300, 'Lista de alunos para assinatura dos pais', 0, 'module/Reports/AssinaturaDosPais', '_self', 1, 15, 192);
INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,999870,1,1,1);

-- undo

DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 999870;
DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999870;
DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999870;