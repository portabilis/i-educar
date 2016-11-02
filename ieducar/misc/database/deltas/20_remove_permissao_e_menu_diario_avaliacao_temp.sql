-- //

--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

DELETE FROM portal.menu_funcionario WHERE ref_cod_menu_submenu = 927;
DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 927;
DELETE FROM pmicontrolesis.menu WHERE ref_cod_menu_submenu = 927;
DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 927;

-- //@UNDO

INSERT INTO menu_submenu VALUES (927, 55, 2, 'Diário de Frequência Temporário', 'educar_relatorio_diario_classe_temp.php', '', 3);
INSERT INTO pmicontrolesis.menu VALUES (nextval('menu_cod_menu_seq'::regclass), 927, 21127, 'Diário de Frequência Temp.', 5, 'educar_relatorio_diario_classe_temp.php', '_self', 1, 15, 1);
INSERT INTO menu_tipo_usuario VALUES (1, 927, 1, 1, 1);
INSERT INTO menu_tipo_usuario VALUES (2, 927, 1, 1, 1);
INSERT INTO menu_funcionario VALUES (1, 1, 1, 927);

-- //