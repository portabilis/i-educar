-- Cria menu de exportação de usuários
-- @author   Paula Bonot <bonot@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

INSERT INTO portal.menu_submenu VALUES (999862, 25, 2,'Exporta&ccedil;&atilde;o de Usu&aacute;rios', 'educar_exportacao_usuarios.php', NULL, 3);
INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,999862,1,1,1);

--Undo

DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 999862;
DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999862;
