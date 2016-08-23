-- Cria menu de configurações gerais
-- @author   Caroline Salib <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

INSERT INTO portal.menu_submenu VALUES (999873, 25, 2,'Configurações gerais', 'educar_configuracoes_gerais.php', NULL, 3);
INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,999873,1,1,1);

--Undo

DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 999873;
DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999873;
